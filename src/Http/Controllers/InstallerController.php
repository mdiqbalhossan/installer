<?php
namespace Softmax\Installer\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Softmax\Installer\Facades\Installer;
use Exception;
use Illuminate\Support\Facades\Log;

class InstallerController extends Controller
{
    /**
     * Show the installer start page
     */
    public function start()
    {
        // If already installed, redirect to main application
        if (Installer::isInstalled() && Installer::verifyEncryptionKey()) {
            return redirect('/');
        }

        return view('softmax-installer::installer');
    }

    /**
     * Get system requirements and permissions
     */
    public function getSystemInfo()
    {
        try {
            $requirements = Installer::checkRequirements();
            $permissions = Installer::checkPermissions();
            
            $allRequirementsMet = collect($requirements)->every(fn($req) => $req['installed']);
            $allPermissionsOk = collect($permissions)->every(fn($perm) => $perm['writable']);
            
            return response()->json([
                'success' => true,
                'requirements' => $requirements,
                'permissions' => $permissions,
                'can_proceed' => $allRequirementsMet && $allPermissionsOk
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check system requirements: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate license with core system
     */
    public function validateLicense(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customerId' => 'required|string',
            'licenseKey' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide both Customer ID and License Key.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $domain = $request->getHost();
            $result = Installer::validateLicense(
                $request->customerId,
                $request->licenseKey,
                $domain
            );            

            return response()->json($result);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'License validation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test database connection
     */
    public function testDatabase(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'host' => 'required|string',
            'port' => 'nullable|integer|min:1|max:65535',
            'name' => 'required|string',
            'username' => 'required|string',
            'password' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide valid database credentials.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $credentials = [
                'host' => $request->host,
                'port' => $request->port ?: 3306,
                'database' => $request->name,
                'username' => $request->username,
                'password' => $request->password,
            ];

            $result = Installer::testDatabaseConnection($credentials);
            return response()->json($result);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Database test failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Complete the installation process
     */
    public function install(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // License data
            'customer_id' => 'required|string',
            'license_key' => 'required|string',
            
            // Database credentials
            'db_host' => 'required|string',
            'db_port' => 'nullable|integer|min:1|max:65535',
            'db_name' => 'required|string',
            'db_username' => 'required|string',
            'db_password' => 'nullable|string',
            
            // Application settings
            'software_name' => 'required|string|max:255',
            'software_url' => 'required|url',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|max:255',
            'admin_password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide all required information.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Re-validate license
            $licenseResult = Installer::validateLicense(
                $request->customer_id,
                $request->license_key,
                $request->getHost()
            );

            if (!$licenseResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'License validation failed: ' . $licenseResult['message']
                ], 400);
            }

            // Test database connection again
            $dbCredentials = [
                'host' => $request->db_host,
                'port' => $request->db_port ?: 3306,
                'database' => $request->db_name,
                'username' => $request->db_username,
                'password' => $request->db_password,
            ];

            $dbResult = Installer::testDatabaseConnection($dbCredentials);
            if (!$dbResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Database connection failed: ' . $dbResult['message']
                ], 400);
            }

            // Update environment file
            $envData = [
                'APP_NAME' => $request->software_name,
                'APP_URL' => $request->software_url,
                'ASSETS_URL' => rtrim($request->software_url, '/').'/public/',
                'DB_CONNECTION' => 'mysql',
                'DB_HOST' => $request->db_host,
                'DB_PORT' => $request->db_port ?: 3306,
                'DB_DATABASE' => $request->db_name,
                'DB_USERNAME' => $request->db_username,
                'DB_PASSWORD' => $request->db_password,
            ];

            Installer::updateEnvironment($envData);

            // Update database configuration for current request
            Config::set('database.default', 'mysql');
            Config::set('database.connections.mysql.host', $request->db_host);
            Config::set('database.connections.mysql.port', $request->db_port ?: 3306);
            Config::set('database.connections.mysql.database', $request->db_name);
            Config::set('database.connections.mysql.username', $request->db_username);
            Config::set('database.connections.mysql.password', $request->db_password);

            // Reconnect to database
            DB::purge('mysql');
            DB::reconnect('mysql');

            // Run migrations
            $migrationResult = Installer::runMigrations();
            if (!$migrationResult['success']) {
                Installer::cleanupInstallation();
                return response()->json([
                    'success' => false,
                    'message' => 'Migration failed: ' . $migrationResult['message']
                ], 500);
            }

            // Create admin user
            $adminResult = Installer::createAdminUser([
                'name' => $request->admin_name,
                'email' => $request->admin_email,
                'password' => $request->admin_password,
            ]);

            if (!$adminResult['success']) {
                Installer::cleanupInstallation();
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create admin user: ' . $adminResult['message']
                ], 500);
            }

            // Register installation with core system
            $registrationData = [
                'customer_id' => $request->customer_id,
                'license_key' => $request->license_key,
                'domain' => $request->getHost(),
                'software_name' => $request->software_name,
                'software_url' => $request->software_url,
                'admin_email' => $request->admin_email,
            ];

            $registrationResult = Installer::registerInstallation($registrationData);
            
            // Note: We don't fail installation if registration fails
            // as it might be a temporary network issue
            
            // Create installation lock files
            Installer::createInstallationLock([
                'customer_id' => $request->customer_id,
                'license_key' => $request->license_key,
                'domain' => $request->getHost(),
                'software_name' => $request->software_name,
                'admin_email' => $request->admin_email,
                'registration_success' => $registrationResult['success'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Installation completed successfully!',
                'redirect_url' => $request->software_url,
                'registration_status' => $registrationResult['success']
            ]);

        } catch (Exception $e) {
            // Clean up on failure
            Installer::cleanupInstallation();
            
            return response()->json([
                'success' => false,
                'message' => 'Installation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset installation (for development purposes)
     */
    public function reset()
    {
        if (app()->environment('production')) {
            abort(403, 'Installation reset is not allowed in production.');
        }

        try {
            Installer::cleanupInstallation();
            
            return response()->json([
                'success' => true,
                'message' => 'Installation reset successfully.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Reset failed: ' . $e->getMessage()
            ], 500);
        }
    }
}

<?php
namespace Softmax\Installer\Services;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Softmax\Installer\Traits\InstallationHelpers;

class InstallerService
{
    use InstallationHelpers;

    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Check if the application is already installed
     */
    public function isInstalled(): bool
    {
        $lockFile = storage_path(config('softmax.installer.installation.lock_file'));
        return file_exists($lockFile);
    }

    /**
     * Verify encryption key matches stored key
     */
    public function verifyEncryptionKey(): bool
    {
        if (! $this->isInstalled()) {
            return false;
        }

        $keyFile = storage_path(config('softmax.installer.installation.encryption_key_file'));

        if (! file_exists($keyFile)) {
            return false;
        }

        try {
            $storedKey  = file_get_contents($keyFile);
            $currentKey = config('app.key');

            return hash_equals($storedKey, $currentKey);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Check PHP extensions requirements
     */
    public function checkRequirements(): array
    {
        $extensions = config('softmax.installer.requirements.php_extensions', []);
        $results    = [];

        foreach ($extensions as $extension) {
            $results[] = [
                'name'      => $extension,
                'required'  => true,
                'installed' => extension_loaded($extension),
                'status'    => extension_loaded($extension) ? 'success' : 'error',
            ];
        }

        return $results;
    }

    /**
     * Check directory permissions
     */
    public function checkPermissions(): array
    {
        $directories = config('softmax.installer.requirements.directories', []);
        $results     = [];

        foreach ($directories as $directory) {
            $path     = base_path($directory);
            $writable = is_writable($path);

            $results[] = [
                'path'     => $directory,
                'writable' => $writable,
                'status'   => $writable ? 'success' : 'error',
            ];
        }

        return $results;
    }

    /**
     * Validate license with core system
     */
    public function validateLicense(string $customerId, string $licenseKey, string $domain): array
    {
        try {
            $cleanDomain = $this->cleanDomain($domain);

            $response = Http::timeout(config('softmax.installer.api_timeout'))
                ->post(config('softmax.installer.api_base') . '/api/validate-license', [
                    'customer_id'  => $customerId,
                    'license_key'  => $licenseKey,
                    'domain'       => $cleanDomain,
                    'product_code' => config('softmax.installer.product_code'),
                    'version'      => '1.0.0',
                ]);

            $data = $response->json();

            // Check if the license server returned an error in JSON
            if (($data['status'] ?? '') === 'error') {
                return [
                    'success' => false,
                    'message' => $data['message'] ?? 'Invalid license credentials.',
                    'errors'  => $data['data']['errors'] ?? null,
                ];
            }

            // Check if license is valid
            if (!($data['data']['is_valid'] ?? false)) {
                return [
                    'success' => false,
                    'message' => $data['data']['errors'][0] ?? 'Invalid license credentials.',
                ];
            }

            return [
                'success' => true,
                'data'    => $data,
                'message' => 'License validated successfully.',
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'License validation failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Test database connection
     */
    public function testDatabaseConnection(array $credentials): array
    {
        try {
            $config = [
                'driver'    => 'mysql',
                'host'      => $credentials['host'],
                'port'      => $credentials['port'] ?? 3306,
                'database'  => $credentials['database'],
                'username'  => $credentials['username'],
                'password'  => $credentials['password'],
                'charset'   => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ];

            // Create a temporary connection
            $dsn        = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']}";
            $connection = new \PDO(
                $dsn,
                $config['username'],
                $config['password'],
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_TIMEOUT => config('softmax.installer.database.timeout', 10),
                ]
            );

            // Test basic query
            $connection->query('SELECT 1');

            return [
                'success' => true,
                'message' => 'Database connection successful.',
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Database connection failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Update environment file
     */
    public function updateEnvironment(array $data): void
    {
        $envPath    = base_path('.env');
        $envContent = file_exists($envPath) ? file_get_contents($envPath) : '';

        foreach ($data as $key => $value) {
            $value   = $this->escapeEnvValue($value);
            $pattern = "/^{$key}=.*$/m";

            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, "{$key}={$value}", $envContent);
            } else {
                $envContent .= PHP_EOL . "{$key}={$value}";
            }
        }

        // Ensure proper line endings
        $envContent = str_replace(["\r\n", "\r"], "\n", $envContent);

        file_put_contents($envPath, $envContent, LOCK_EX);
    }

    /**
     * Run database migrations
     */
    public function runMigrations(): array
    {
        try {
            // Clear any cached config
            Artisan::call('config:clear');

            Artisan::call('migrate:fresh', [
                '--seed' => true,
            ]);

            return [
                'success' => true,
                'message' => 'Database migrations completed successfully.',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Migration failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Create admin user
     */
    public function createAdminUser(array $data): array
    {
        try {
            $userModel = config('auth.providers.users.model', 'App\\Models\\User');

            // Check if email already exists
            if ($userModel::where('email', $data['email'])->exists()) {
                // Update existing user
                $user                    = $userModel::where('email', $data['email'])->first();
                $user->name              = $data['name'];
                $user->password          = Hash::make($data['password']);
                $user->email_verified_at = now();
            } else {
                // Create new user
                $user                    = new $userModel();
                $user->name              = $data['name'];
                $user->email             = $data['email'];
                $user->password          = Hash::make($data['password']);
                $user->email_verified_at = now();
            }

            $user->save();

            // Assign admin role if Spatie permission package is available
            if (method_exists($user, 'assignRole')) {
                $user->assignRole(config('softmax.installer.admin.default_role', 'admin'));
            }

            return [
                'success' => true,
                'message' => 'Admin user created successfully.',
                'user_id' => $user->id,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to create admin user: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Register installation with core system
     */
    public function registerInstallation(array $data): array
    {
        try {
            $response = Http::timeout(config('softmax.installer.api_timeout'))
                ->post(config('softmax.installer.api_base') . '/api/register-installation', [
                    'customer_id'       => $data['customer_id'],
                    'license_key'       => $data['license_key'],
                    'domain'            => $this->cleanDomain($data['domain']),
                    'software_name'     => $data['software_name'],
                    'software_url'      => $data['software_url'],
                    'admin_email'       => $data['admin_email'],
                    'installation_date' => now()->toISOString(),
                    'software'          => 'smartbill',
                    'version'           => '1.0.0',
                    'server_info'       => $this->getServerInfo(),
                ]);
                Log::info('Installation registration response', ['response' => $response->json()]);
            if (! $response->successful()) {
                return [
                    'success' => false,
                    'message' => 'Failed to register installation with core system.',
                ];
            }

            return [
                'success' => true,
                'message' => 'Installation registered successfully.',
                'data'    => $response->json(),
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Create installation lock files
     */
    public function createInstallationLock(array $licenseData): void
    {
        $storageDir = storage_path('installed');

        if (! is_dir($storageDir)) {
            mkdir($storageDir, 0755, true);
        }

        // Create lock file
        $lockFile = storage_path(config('softmax.installer.installation.lock_file'));
        file_put_contents($lockFile, json_encode([
            'installed_at'      => now()->toISOString(),
            'version'           => '1.0.0',
            'domain'            => $this->cleanDomain(request()->getHost()),
            'installer_version' => '1.0.0',
        ]), LOCK_EX);

        // Store encryption key
        $keyFile = storage_path(config('softmax.installer.installation.encryption_key_file'));
        file_put_contents($keyFile, config('app.key'), LOCK_EX);

        // Store license information (encrypted)
        $licenseFile = storage_path(config('softmax.installer.installation.license_file'));
        file_put_contents($licenseFile, json_encode($licenseData), LOCK_EX);
    }

    /**
     * Get installation progress
     */
    public function getInstallationProgress(): array
    {
        return [
            'requirements' => $this->checkRequirements(),
            'permissions'  => $this->checkPermissions(),
            'server_info'  => $this->getServerInfo(),
        ];
    }

    /**
     * Clean up installation files on failure
     */
    public function cleanupInstallation(): void
    {
        $files = [
            storage_path(config('softmax.installer.installation.lock_file')),
            storage_path(config('softmax.installer.installation.encryption_key_file')),
            storage_path(config('softmax.installer.installation.license_file')),
        ];

        foreach ($files as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }

        // Remove installed directory if empty
        $installedDir = storage_path('installed');
        if (is_dir($installedDir) && count(scandir($installedDir)) === 2) {
            rmdir($installedDir);
        }
    }

    /**
     * Escape environment value
     */
    private function escapeEnvValue($value): string
    {
        if (is_null($value)) {
            return '';
        }

        $value = (string) $value;

        if (preg_match('/\s/', $value) || strpos($value, '"') !== false) {
            return '"' . str_replace('"', '\\"', $value) . '"';
        }

        return $value;
    }
}

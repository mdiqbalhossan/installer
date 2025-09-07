<?php
namespace Softmax\Installer\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static bool isInstalled()
 * @method static bool verifyEncryptionKey()
 * @method static array checkRequirements()
 * @method static array checkPermissions()
 * @method static array validateLicense(string $customerId, string $licenseKey, string $domain)
 * @method static array testDatabaseConnection(array $credentials)
 * @method static void updateEnvironment(array $data)
 * @method static array runMigrations()
 * @method static array createAdminUser(array $data)
 * @method static array registerInstallation(array $data)
 * @method static void createInstallationLock(array $licenseData)
 * @method static array getInstallationProgress()
 * @method static void cleanupInstallation()
 * 
 * @see \Softmax\Installer\Services\InstallerService
 */
class Installer extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'softmax.installer';
    }
}

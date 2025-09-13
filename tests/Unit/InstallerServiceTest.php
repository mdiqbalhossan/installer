<?php

namespace Softmax\Installer\Tests\Unit;

use Softmax\Installer\Tests\TestCase;
use Softmax\Installer\Services\InstallerService;
use Softmax\Installer\Facades\Installer;

class InstallerServiceTest extends TestCase
{
    protected $installerService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->installerService = app(InstallerService::class);
    }

    /** @test */
    public function it_can_check_if_application_is_not_installed()
    {
        $this->assertFalse($this->installerService->isInstalled());
    }

    /** @test */
    public function it_can_check_requirements()
    {
        $requirements = $this->installerService->checkRequirements();
        
        $this->assertIsArray($requirements);
        $this->assertNotEmpty($requirements);
        
        // Check that each requirement has required structure
        foreach ($requirements as $requirement) {
            $this->assertArrayHasKey('name', $requirement);
            $this->assertArrayHasKey('installed', $requirement);
            $this->assertIsBool($requirement['installed']);
        }
    }

    /** @test */
    public function it_can_check_permissions()
    {
        $permissions = $this->installerService->checkPermissions();
        
        $this->assertIsArray($permissions);
        $this->assertNotEmpty($permissions);
        
        // Check that each permission has required structure
        foreach ($permissions as $permission) {
            $this->assertArrayHasKey('directory', $permission);
            $this->assertArrayHasKey('writable', $permission);
            $this->assertIsBool($permission['writable']);
        }
    }

    /** @test */
    public function facade_works_correctly()
    {
        $this->assertFalse(Installer::isInstalled());
        $this->assertIsArray(Installer::checkRequirements());
        $this->assertIsArray(Installer::checkPermissions());
    }

    /** @test */
    public function encryption_key_verification_returns_false_when_not_installed()
    {
        $this->assertFalse($this->installerService->verifyEncryptionKey());
    }

    /** @test */
    public function it_can_validate_database_connection_with_invalid_credentials()
    {
        $credentials = [
            'host' => 'invalid-host',
            'port' => '3306',
            'database' => 'test_db',
            'username' => 'test_user',
            'password' => 'test_pass'
        ];

        $result = $this->installerService->testDatabaseConnection($credentials);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('message', $result);
    }
}
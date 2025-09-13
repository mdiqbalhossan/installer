<?php

namespace Softmax\Installer\Tests\Feature;

use Softmax\Installer\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InstallerControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_access_installer_start_page()
    {
        $response = $this->get(route('softmax.installer.start'));
        
        $response->assertStatus(200);
        $response->assertViewIs('softmax-installer::installer');
    }

    /** @test */
    public function it_can_get_system_info()
    {
        $response = $this->get(route('softmax.installer.system-info'));
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'requirements',
            'permissions',
            'can_proceed'
        ]);
        
        $data = $response->json();
        $this->assertTrue($data['success']);
        $this->assertIsArray($data['requirements']);
        $this->assertIsArray($data['permissions']);
        $this->assertIsBool($data['can_proceed']);
    }

    /** @test */
    public function it_can_handle_license_validation_request()
    {
        $response = $this->postJson(route('softmax.installer.validate-license'), [
            'customer_id' => 'test-customer',
            'license_key' => 'test-license',
            'domain' => 'test.local'
        ]);
        
        // Should return validation response (likely failure in test environment)
        $response->assertJsonStructure([
            'success',
            'message'
        ]);
    }

    /** @test */
    public function it_requires_license_validation_fields()
    {
        $response = $this->postJson(route('softmax.installer.validate-license'), []);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['customer_id', 'license_key', 'domain']);
    }

    /** @test */
    public function it_can_test_database_connection()
    {
        $response = $this->postJson(route('softmax.installer.test-database'), [
            'host' => 'localhost',
            'port' => '3306',
            'database' => 'test_db',
            'username' => 'test_user',
            'password' => 'test_password'
        ]);
        
        $response->assertJsonStructure([
            'success',
            'message'
        ]);
    }

    /** @test */
    public function it_requires_database_connection_fields()
    {
        $response = $this->postJson(route('softmax.installer.test-database'), []);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['host', 'database', 'username']);
    }

    /** @test */
    public function installer_routes_are_accessible_without_installation()
    {
        // Test that installer routes work even when not installed
        $routes = [
            'softmax.installer.start',
            'softmax.installer.system-info'
        ];

        foreach ($routes as $route) {
            $response = $this->get(route($route));
            $response->assertSuccessful();
        }
    }
}
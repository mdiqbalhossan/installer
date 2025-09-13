<?php

namespace Softmax\Installer\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Softmax\Installer\InstallerServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            InstallerServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Installer' => \Softmax\Installer\Facades\Installer::class,
        ];
    }

    protected function defineEnvironment($app)
    {
        // Setup the application environment for testing
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        // Set app key for testing
        $app['config']->set('app.key', 'base64:AckfSECXIvnK5r28GVIWUAxmbBbUPsJO');
        $app['config']->set('app.cipher', 'AES-256-CBC');
    }

    protected function defineDatabaseMigrations()
    {
        // Load Laravel default migrations for testing
        $this->loadLaravelMigrations(['--database' => 'testbench']);
    }
}
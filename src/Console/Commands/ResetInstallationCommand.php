<?php

namespace Softmax\Installer\Console\Commands;

use Illuminate\Console\Command;
use Softmax\Installer\Facades\Installer;

class ResetInstallationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'installer:reset 
                            {--force : Force reset without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the installation (development only)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Only allow in non-production environments
        if (app()->environment('production')) {
            $this->error('This command cannot be run in production environment!');
            return 1;
        }

        if (!Installer::isInstalled()) {
            $this->info('Application is not installed. Nothing to reset.');
            return 0;
        }

        if (!$this->option('force')) {
            if (!$this->confirm('Are you sure you want to reset the installation? This will remove all installation files.')) {
                $this->info('Installation reset cancelled.');
                return 0;
            }
        }

        $this->info('Resetting installation...');

        try {
            Installer::cleanupInstallation();
            $this->info('✓ Installation reset successfully!');
            $this->info('You can now run the installer again by visiting: /softmax-installer');
            
            return 0;
        } catch (\Exception $e) {
            $this->error('Failed to reset installation: ' . $e->getMessage());
            return 1;
        }
    }
}
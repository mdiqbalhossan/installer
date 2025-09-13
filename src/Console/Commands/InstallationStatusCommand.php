<?php

namespace Softmax\Installer\Console\Commands;

use Illuminate\Console\Command;
use Softmax\Installer\Facades\Installer;

class InstallationStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'installer:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the installation status of the application';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking installation status...');

        if (Installer::isInstalled()) {
            $this->info('✓ Application is installed');
            
            if (Installer::verifyEncryptionKey()) {
                $this->info('✓ Encryption key is valid');
            } else {
                $this->error('✗ Encryption key verification failed');
                return 1;
            }
        } else {
            $this->warn('✗ Application is not installed');
            $this->info('Run the installer by visiting: /softmax-installer');
            return 1;
        }

        $this->newLine();
        $this->info('Installation status check completed successfully!');
        
        return 0;
    }
}
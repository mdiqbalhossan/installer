<?php

namespace Softmax\Installer\Traits;

trait InstallationHelpers
{
    /**
     * Generate a secure random key
     */
    protected function generateSecureKey(): string
    {
        return 'base64:' . base64_encode(random_bytes(32));
    }

    /**
     * Check if running in CLI mode
     */
    protected function isCliMode(): bool
    {
        return php_sapi_name() === 'cli';
    }

    /**
     * Get server software information
     */
    protected function getServerInfo(): array
    {
        return [
            'php_version' => PHP_VERSION,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'operating_system' => PHP_OS,
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
        ];
    }

    /**
     * Format bytes to human readable format
     */
    protected function formatBytes(int $size, int $precision = 2): string
    {
        $base = log($size, 1024);
        $suffixes = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
    }

    /**
     * Validate URL format
     */
    protected function isValidUrl(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Validate email format
     */
    protected function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Clean and validate domain name
     */
    protected function cleanDomain(string $domain): string
    {
        // Remove protocol if present
        $domain = preg_replace('/^https?:\/\//', '', $domain);
        
        // Remove www if present
        $domain = preg_replace('/^www\./', '', $domain);
        
        // Remove trailing slash
        $domain = rtrim($domain, '/');
        
        // Remove port if present
        $domain = preg_replace('/:\d+$/', '', $domain);
        
        return strtolower($domain);
    }
}

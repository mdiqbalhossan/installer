<?php

namespace Softmax\Installer\Exceptions;

use Exception;

class InstallerException extends Exception
{
    /**
     * Create a new installer exception instance.
     */
    public function __construct(string $message = '', int $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

class RequirementException extends InstallerException
{
    protected array $failedRequirements = [];

    /**
     * Create a new requirement exception instance.
     */
    public function __construct(string $message = '', array $failedRequirements = [], int $code = 0, Exception $previous = null)
    {
        $this->failedRequirements = $failedRequirements;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the failed requirements.
     */
    public function getFailedRequirements(): array
    {
        return $this->failedRequirements;
    }
}

class LicenseException extends InstallerException
{
    protected array $licenseData = [];

    /**
     * Create a new license exception instance.
     */
    public function __construct(string $message = '', array $licenseData = [], int $code = 0, Exception $previous = null)
    {
        $this->licenseData = $licenseData;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the license data.
     */
    public function getLicenseData(): array
    {
        return $this->licenseData;
    }
}

class DatabaseException extends InstallerException
{
    protected array $connectionData = [];

    /**
     * Create a new database exception instance.
     */
    public function __construct(string $message = '', array $connectionData = [], int $code = 0, Exception $previous = null)
    {
        $this->connectionData = $connectionData;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the connection data.
     */
    public function getConnectionData(): array
    {
        return $this->connectionData;
    }
}

class InstallationException extends InstallerException
{
    protected array $installationData = [];

    /**
     * Create a new installation exception instance.
     */
    public function __construct(string $message = '', array $installationData = [], int $code = 0, Exception $previous = null)
    {
        $this->installationData = $installationData;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the installation data.
     */
    public function getInstallationData(): array
    {
        return $this->installationData;
    }
}

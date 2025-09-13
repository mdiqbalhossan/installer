# SoftMax Installer Package

[![Latest Stable Version](https://poser.pugx.org/softmax/installer/v/stable)](https://packagist.org/packages/softmax/installer)
[![Total Downloads](https://poser.pugx.org/softmax/installer/downloads)](https://packagist.org/packages/softmax/installer)
[![License](https://poser.pugx.org/softmax/installer/license)](https://packagist.org/packages/softmax/installer)

A comprehensive Laravel installation wizard package that provides a complete setup process for Laravel applications with license validation, system requirements checking, and guided installation flow. Perfect for commercial Laravel applications that need professional installation experience.

## Features

- 🔐 **License Validation**: Integrate with SoftMax core system for license verification
- ⚙️ **System Requirements Check**: Automatically verify PHP version, extensions, and server requirements
- 📊 **Directory Permissions**: Check and validate directory write permissions
- 🗄️ **Database Configuration**: Guided setup and testing for database connections
- 🌍 **Environment Setup**: Configure essential .env variables securely
- 👤 **Admin User Creation**: Set up the initial administrator account with role assignment
- 🔄 **Migration & Seeding**: Run database migrations automatically
- 🔒 **Installation Lock**: Prevent re-installation and validate encryption keys
- 🎨 **Beautiful UI**: Modern, responsive installation wizard interface
- 🛡️ **Security**: CSRF protection, input validation, and secure key storage
- 📡 **API Integration**: Register installation with core licensing system

## Requirements

- PHP 8.1 or higher
- Laravel 10.x or 11.x
- MySQL/MariaDB database
- Required PHP extensions: PDO, OpenSSL, Mbstring, ZIP, XML, cURL, GD, Fileinfo, Tokenizer, JSON

## Installation

Install the package via Composer:

```bash
composer require softmax/installer
```

If you encounter a minimum stability error, you can install a specific stable version:

```bash
composer require softmax/installer:^1.0
```

For Laravel 10+, the package will automatically register its service provider and middleware.

## Quick Start

1. **Install the package:**
```bash
composer require softmax/installer
```

If you encounter a minimum stability error, use:
```bash
composer require softmax/installer:^1.0
```

2. **Publish the configuration:**
```bash
php artisan vendor:publish --tag=softmax-installer-config
```

3. **Configure your environment variables:**
```env
SOFTMAX_API_BASE=https://api.soft-max.app
SOFTMAX_API_TIMEOUT=30
```

4. **Access the installer:**
Navigate to `/softmax-installer` in your browser to start the installation process.

## Installation Process

The installer follows a comprehensive multi-step process:

1. **System Check**: Verifies if application is already installed
2. **Requirements Validation**: Checks PHP extensions and system requirements
3. **Permissions Check**: Validates directory write permissions
4. **License Validation**: Verifies customer ID and license key with API
5. **Database Configuration**: Tests and configures database connection
6. **Environment Setup**: Configures application name, URL, and essential settings
7. **Admin Account**: Creates the initial administrator user
8. **Final Installation**: Runs migrations and registers with core system
9. **Completion**: Shows success message with launch button

## Configuration

The package configuration can be customized in `config/softmax-installer.php`:

```php
return [
    /*
    |--------------------------------------------------------------------------
    | API Configuration
    |--------------------------------------------------------------------------
    */
    'api_base' => env('SOFTMAX_API_BASE', 'https://api.soft-max.app'),
    'api_timeout' => env('SOFTMAX_API_TIMEOUT', 30),
    'product_code' => '12345',

    /*
    |--------------------------------------------------------------------------
    | Installation Configuration
    |--------------------------------------------------------------------------
    */
    'installation' => [
        'lock_file' => 'installed/installer.lock',
        'encryption_key_file' => 'installed/encryption_key',
        'license_file' => 'installed/license.json',
    ],

    /*
    |--------------------------------------------------------------------------
    | Requirements Configuration
    |--------------------------------------------------------------------------
    */
    'requirements' => [
        'php_extensions' => [
            'pdo', 'pdo_mysql', 'openssl', 'mbstring', 'zip',
            'xml', 'curl', 'gd', 'fileinfo', 'tokenizer', 'json',
        ],
        'directories' => [
            'storage', 'bootstrap/cache', 'storage/app',
            'storage/framework', 'storage/logs',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Configuration
    |--------------------------------------------------------------------------
    */
    'database' => [
        'default_host' => 'localhost',
        'default_port' => '3306',
        'timeout' => 10,
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Configuration
    |--------------------------------------------------------------------------
    */
    'admin' => [
        'default_role' => 'Super Admin',
        'min_password_length' => 8,
    ],
];
```

## Environment Variables

Add these variables to your `.env` file:

```env
# SoftMax Installer Configuration
SOFTMAX_API_BASE=https://api.soft-max.app
SOFTMAX_API_TIMEOUT=30

# Your product configuration
SOFTMAX_PRODUCT_CODE=your_product_code
```

## Usage

### Facade Usage

The package provides a convenient facade for checking installation status:

```php
use Softmax\Installer\Facades\Installer;

// Check if application is installed
if (Installer::isInstalled()) {
    // Application is installed
}

// Verify encryption key
if (Installer::verifyEncryptionKey()) {
    // Key is valid
}

// Check system requirements
$requirements = Installer::checkRequirements();
$permissions = Installer::checkPermissions();
```

### API Endpoints

The installer provides several API endpoints:

- `GET /softmax-installer/system-info` - Get system requirements and permissions
- `POST /softmax-installer/validate-license` - Validate license credentials
- `POST /softmax-installer/test-database` - Test database connection
- `POST /softmax-installer/install` - Complete installation process
- `POST /softmax-installer/reset` - Reset installation (development only)

### Middleware

The package automatically applies the [`RedirectIfNotInstalled`](packages/softmax/installer/src/Http/Middleware/RedirectIfNotInstalled.php) middleware to all web routes. This middleware:

- Redirects to installer if application is not installed
- Validates encryption key matches stored key
- Allows access to installer routes and assets

## Customization

### Publishing Views

Customize the installer's appearance by publishing the view files:

```bash
php artisan vendor:publish --tag=softmax-installer-views
```

Views will be published to `resources/views/vendor/softmax-installer/`.

### Custom Installation Steps

Extend the [`InstallerService`](packages/softmax/installer/src/Services/InstallerService.php) to add custom installation logic:

```php
use Softmax\Installer\Services\InstallerService;

class CustomInstallerService extends InstallerService
{
    public function customInstallationStep(array $data): array
    {
        // Your custom installation logic
        
        return [
            'success' => true,
            'message' => 'Custom step completed successfully.'
        ];
    }
}
```

### Exception Handling

The package includes custom exceptions for different scenarios:

```php
use Softmax\Installer\Exceptions\{
    InstallerException,
    RequirementException,
    LicenseException,
    DatabaseException,
    InstallationException
};

try {
    // Installation code
} catch (LicenseException $e) {
    // Handle license validation errors
} catch (DatabaseException $e) {
    // Handle database connection errors
}
```

## Development

### Reset Installation

For development purposes, you can reset the installation:

```bash
# Remove installation files manually
rm -rf storage/installed/

# Or use the API endpoint (non-production only)
curl -X POST http://your-app.test/softmax-installer/reset
```

### Testing

Run the package tests:

```bash
composer test
```

The package includes comprehensive validation for:
- PHP extension requirements
- Directory permissions
- License validation
- Database connectivity
- Admin user creation

## Security

- ✅ License validation with remote API
- ✅ Secure encryption key storage and verification
- ✅ CSRF protection on all forms
- ✅ Input validation and sanitization
- ✅ Production environment restrictions
- ✅ Prevents reinstallation attempts

## Integration with Laravel

The package integrates seamlessly with Laravel:

- **Service Provider**: [`InstallerServiceProvider`](packages/softmax/installer/src/InstallerServiceProvider.php) automatically registers services
- **Facade**: [`Installer`](packages/softmax/installer/src/Facades/Installer.php) provides convenient static methods
- **Middleware**: [`RedirectIfNotInstalled`](packages/softmax/installer/src/Http/Middleware/RedirectIfNotInstalled.php) protects your application
- **Routes**: Automatically loaded from [`routes/web.php`](packages/softmax/installer/routes/web.php)
- **Views**: Responsive UI built with Tailwind CSS and Alpine.js

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

If you discover a security vulnerability within this package, please send an e-mail to support@soft-max.app. All security vulnerabilities will be promptly addressed.

## License

This package is proprietary software owned by SoftMax. Unauthorized distribution is prohibited.

## Support

- **Documentation**: Visit our [documentation site](https://docs.soft-max.app)
- **Email Support**: support@soft-max.app
- **Issue Tracker**: [GitHub Issues](https://github.com/softmax/installer/issues)

## Credits

- [SoftMax Team](https://github.com/softmax)
- [All Contributors](../../contributors)

---

Made with ❤️ by [SoftMax](https://soft-max.app)
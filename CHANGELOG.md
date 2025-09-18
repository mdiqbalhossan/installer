# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- **SonarQube Integration**: Complete code quality analysis with SonarQube
- **Quality Gates**: Comprehensive quality gates for coverage, maintainability, reliability, and security
- **Enhanced CI/CD**: SonarQube workflow for automated code analysis
- **Coverage Reporting**: Multi-format coverage reports (Clover, HTML, Cobertura)
- **Code Quality Metrics**: Technical debt, duplication, and complexity tracking
- **Security Analysis**: Vulnerability and security hotspot detection
- **Documentation**: Complete SonarQube integration guide and best practices
- Comprehensive test suite with PHPUnit
- Code quality tools (Laravel Pint, PHPStan)
- GitHub Actions CI/CD workflow
- Form Request classes for validation
- Enhanced exception handling with specific exception types
- Proper Laravel config naming convention
- Composer scripts for testing and quality checks

### Changed
- **PHPUnit Configuration**: Enhanced for SonarQube compatibility with multiple coverage formats
- **Composer Scripts**: Added SonarQube analysis and coverage scripts
- **CI/CD Pipeline**: Extended with coverage collection and SonarQube integration
- **Build Artifacts**: Organized build outputs for better analysis
- Updated config key from 'softmax.installer' to 'softmax-installer' for Laravel standards
- Improved exception classes with better error context
- Enhanced validation with Form Request classes

### Fixed
- Laravel package standards compliance
- Config naming convention alignment
- Coverage report path compatibility with SonarQube

## [1.0.0] - 2025-09-13

### Added
- Laravel installation wizard package
- License validation system
- System requirements checking
- Database configuration and testing
- Admin user creation
- Environment configuration
- Installation lock mechanism
- Responsive UI with Tailwind CSS
- API endpoints for installation steps
- Middleware for installation protection
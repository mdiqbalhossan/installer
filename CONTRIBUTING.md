# Contributing to SoftMax Installer

Thank you for considering contributing to the SoftMax Installer package! This document outlines how to contribute to this project.

## Development Setup

1. Fork the repository
2. Clone your fork:
   ```bash
   git clone https://github.com/your-username/installer.git
   ```
3. Install dependencies:
   ```bash
   composer install
   ```

## Code Quality Standards

We maintain high code quality standards using automated tools and SonarQube analysis.

### Quality Gates

All code must pass the following quality gates:

#### Coverage
- **New Code Coverage**: Minimum 80%
- **Overall Coverage**: Maintain or improve existing coverage
- **Coverage on New Lines**: 100% on new code

#### Maintainability  
- **Technical Debt Ratio**: Maximum 5%
- **Maintainability Rating**: A rating required
- **Code Smells**: Zero new code smells

#### Reliability
- **Reliability Rating**: A rating required  
- **Bugs**: Zero new bugs
- **Bug-free New Code**: 100%

#### Security
- **Security Rating**: A rating required
- **Vulnerabilities**: Zero new vulnerabilities
- **Security Hotspots**: All reviewed
- **Security Review Rating**: A rating required

#### Duplication
- **Duplicated Lines**: Maximum 3%
- **Duplicated Blocks**: Minimize duplication

### Automated Checks

The following tools run automatically in our CI/CD pipeline:

- **Laravel Pint**: PSR-12 code formatting
- **PHPStan**: Static analysis (Level 5)
- **PHPUnit**: Unit and feature testing
- **SonarQube**: Comprehensive code quality analysis

## Code Standards

We follow Laravel coding standards and use the following tools:

### Code Style
- **Laravel Pint**: For code formatting
- Run: `composer format` or `composer format-check`

### Static Analysis
- **PHPStan**: For static code analysis
- Run: `composer analyse`

### Testing
- **PHPUnit**: For unit and feature tests
- Run: `composer test` or `composer test-coverage`

### Quality Check
- Run all quality checks: `composer quality`
- Run quality checks with coverage: `composer quality-full`

### SonarQube Analysis
- **Prepare for analysis**: `composer sonar-prepare`
- **Run local analysis**: `composer sonar-analysis`
- **View results**: Check SonarQube dashboard after CI/CD run

## Testing

### Running Tests
```bash
# Run all tests
composer test

# Run tests with coverage
composer test-coverage

# Run specific test file
vendor/bin/phpunit tests/Unit/InstallerServiceTest.php
```

### Writing Tests
- Unit tests go in `tests/Unit/`
- Feature tests go in `tests/Feature/`
- Follow existing test patterns
- Use descriptive test method names
- Test both happy path and edge cases

## Pull Request Process

1. **Create a feature branch** from `main`:
   ```bash
   git checkout -b feature/your-feature-name
   ```

2. **Make your changes** following the code standards

3. **Run quality checks**:
   ```bash
   composer quality-full
   ```

4. **Ensure SonarQube compliance**:
   - Maintain minimum 80% code coverage
   - Zero new bugs, vulnerabilities, or security hotspots
   - Keep technical debt ratio below 5%
   - Follow clean code principles

5. **Write/update tests** for your changes

5. **Update documentation** if needed

6. **Submit a pull request** with:
   - Clear description of changes
   - Link to any related issues
   - Screenshots for UI changes

## Code Style Guidelines

### PHP
- Follow PSR-12 coding standard
- Use type hints for all parameters and return types
- Write descriptive variable and method names
- Keep methods focused and single-purpose

### Documentation
- Use PHPDoc blocks for all classes and public methods
- Include parameter and return type documentation
- Add examples for complex methods

### Commits
- Use conventional commit format:
  - `feat:` for new features
  - `fix:` for bug fixes
  - `docs:` for documentation changes
  - `test:` for test additions/changes
  - `refactor:` for code refactoring

## Issue Reporting

When reporting issues, please include:

1. **Environment details**:
   - PHP version
   - Laravel version
   - Package version

2. **Steps to reproduce** the issue

3. **Expected vs actual behavior**

4. **Error messages** if any

5. **Code samples** if relevant

## Security Vulnerabilities

If you discover a security vulnerability, please send an email to support@soft-max.app instead of using the issue tracker.

## Development Guidelines

### Architecture
- Follow Laravel package development best practices
- Use service classes for business logic
- Keep controllers thin
- Use form requests for validation
- Implement proper exception handling

### Database
- Use migrations for any database changes
- Follow Laravel naming conventions
- Add indexes where appropriate

### Configuration
- Use environment variables for sensitive data
- Provide sensible defaults
- Document all configuration options

## License

By contributing to this project, you agree that your contributions will be licensed under the same license as the project.

## Questions?

If you have questions about contributing, feel free to:
- Open an issue for discussion
- Contact us at support@soft-max.app

Thank you for contributing! 🚀
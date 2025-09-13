# Package Release Notes

## v1.0.0 Release

This package has been tagged with v1.0.0 to resolve the Composer installation issue where users encountered:

```
Could not find a version of package softmax/installer matching your minimum-stability (stable).
```

### What was fixed:

1. **Added stable version tag**: Created v1.0.0 tag to mark the current stable state
2. **Updated CHANGELOG**: Properly documented the release with date
3. **Enhanced documentation**: Added installation instructions for stability issues

### For maintainers:

To make this package installable via `composer require softmax/installer`, ensure:

1. The v1.0.0 tag is pushed to the remote repository:
   ```bash
   git push origin v1.0.0
   ```

2. If the package is published on Packagist, the webhook should automatically pick up the new tag

3. Users can then install without version constraints:
   ```bash
   composer require softmax/installer
   ```

### Version constraints:

Users can install using version constraints if needed:
- `composer require softmax/installer:^1.0` - Latest v1.x
- `composer require softmax/installer:1.0.0` - Exact version
- `composer require softmax/installer:dev-main` - Latest development version

### Semantic Versioning:

This package follows [Semantic Versioning](https://semver.org/):
- MAJOR version for incompatible API changes
- MINOR version for backward-compatible functionality additions  
- PATCH version for backward-compatible bug fixes
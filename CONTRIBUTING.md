# Contributing

Thank you for considering contributing to Testa!

## Issues

Please use the [GitHub issue tracker](https://github.com/numaxlab/testa/issues) to report bugs or request features.
Before opening a new issue, search the existing ones to avoid duplicates.

## Pull Requests

1. Fork the repository and create a branch from `main`.
2. Ensure your changes are covered by tests.
3. Run the test suite before submitting:
   ```bash
   composer test
   ```
4. Follow the existing code style (PSR-12).
5. Write a clear commit message and pull request description.

## Testing

This package uses [Pest](https://pestphp.com/) with Orchestra Testbench. Tests use an SQLite in-memory database.

```bash
# Run all tests
composer test

# Run a specific test file
./vendor/bin/pest tests/Unit/Models/Content/PageTest.php

# Run a specific test by name
./vendor/bin/pest --filter="test name"
```

## Code of Conduct

Be respectful and constructive. We follow the [Contributor Covenant](https://www.contributor-covenant.org/).

## Security

If you discover a security vulnerability, please email [adrian@numax.org](mailto:adrian@numax.org) instead of using
the public issue tracker.

## License

By contributing, you agree that your contributions will be licensed under the [MIT License](LICENSE.md).

# Standalone Test Harness

The PHP unit tests for the option management layer are simple scripts that rely on the `tests/wp-stubs.php` shims instead of a full WordPress bootstrap. Run them from the plugin root with the system PHP binary:

```bash
php tests/model-options-test.php
php tests/option-seeding-test.php
```

Each script throws a `RuntimeException` if an assertion fails and prints a success message when the checks pass. The helper stubs reset their in-memory option store between runs, so you can execute the scripts back-to-back while iterating on default seeding logic.

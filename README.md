# Base32H
A PHP implementation of https://base32h.github.io/

### How to install
composer require ariselseng/base32h

### How to use
```php
Base32H::encode(1234567890) === '14RC0NJ'
Base32H::decode('L') === 20

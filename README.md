# Authorized Keys ![CI](https://github.com/pagemachine/authorized-keys/workflows/CI/badge.svg)

Read, edit and write the SSH `authorized_keys` file.

## Installation

    composer require pagemachine/authorized-keys

## Usage

To access the `authorized_keys` file an instance of `Pagemachine\AuthorizedKeys\AuthorizedKeys` must be created, either directly passing the file content to the constructor or using the static `fromFile()` method:

```php
$path = '/home/foo/.ssh/authorized_keys';
$authorizedKeys = AuthorizedKeys::fromFile($path);
```

You can easily iterate all keys in the file, comments and empty lines will be skipped:

```
foreach ($authorizedKeys as $key) {
    // Do something with $key
}
```

To add a key, create an instance of `Pagemachine\AuthorizedKeys\PublicKey` and add it to the file:

```php
// ... load $authorizedKeys ...
$key = new PublicKey('ssh-rsa AAA...');
$authorizedKeys->addKey($key);
```

Notice that each key is only added once, if you add it again, only its options, type and comment are updated accordingly. Thus it's safe to call this method in any case to ensure keys are present.

To remove a key, wrap it in `PublicKey` and remove it from the file:

```php
// ... load $authorizedKeys ...
$key = new PublicKey('ssh-rsa AAA...');
$authorizedKeys->removeKey($key);
```

To close things off, write back the file, comments and empty lines will be left unchanged:

```php
$authorizedKeys->toFile($path);
```

The permissions of the file will be changed to 0600, namely readable and writable by the owner but nobody else.

## Testing

All tests can be executed with the shipped Docker Compose definition:

    docker compose run --rm app composer build

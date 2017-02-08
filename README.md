# Authorized Keys

Read, edit and write the SSH `authorized_keys` file.

## Installation

    composer require pagemachine/authorized-keys

## Usage

To access the `authorized_keys` file an instance of `AuthorizedKeys` must be created, either directly passing the file content to the constructor or using the static `fromFile()` method:

```php

$path = '/home/foo/.ssh/authorized_keys'
$authorizedKeys = AuthorizedKeys::fromFile($path);
```

To add a key, create an instance of `PublicKey` and add it to the file:

```php
$key = new PublicKey('ssh-rsa AAA...');
$authorizedKeys->addKey($key);
```

Notice that each key is only added once, if you add it again, only its options, type and comment are updated accordingly. Thus it's safe to call this method in any case to ensure keys are present.

To remove a key wrap it in `PublicKey` to make it identifiable and remove it from the file:

```php
$key = new PublicKey('ssh-rsa AAA...');
$authorizedKeys->removeKey($key);
```

To close things off, write back the file, all empty lines and comments will be left unchanged:

```php
$authorizedKeys->toFile($path);
```

You can also iterate all keys:

```
foreach ($authorizedKeys as $key) {
    // Do something with $key
}
```

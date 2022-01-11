<?php

declare(strict_types = 1);

namespace Pagemachine\AuthorizedKeys;

/*
 * This file is part of the pagemachine Authorized Keys project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Pagemachine\AuthorizedKeys\Exception\FilePermissionException;
use Pagemachine\AuthorizedKeys\Exception\InvalidKeyException;
use Pagemachine\AuthorizedKeys\InvalidPublicKey;

/**
 * Manages the authorized_keys file
 */
final class AuthorizedKeys implements \IteratorAggregate
{
    /**
     * Lines of the file
     */
    private array $lines = [];

    /**
     * Map of keys to file lines
     */
    private array $keyLines = [];

    public function __construct(string $content = null)
    {
        if (!empty($content)) {
            $this->lines = $this->parse($content);
        }
    }

    /**
     * Creates a new instance from a file
     *
     * @throws FilePermissionException if the authorized_keys file cannot be read
     */
    public static function fromFile(string $file): self
    {
        $content = @file_get_contents($file);

        if ($content === false) {
            throw new FilePermissionException(sprintf('Could not read file "%s"', $file), 1486563469);
        }

        return new self($content);
    }

    /**
     * Writes all content to the filesystem
     *
     * Also ensure that the written file has the recommended permissions,
     * namely only readable and writable to the current user
     *
     * @param string $file path of the authorized_keys file
     * @throws FilePermissionException if the authorized_keys file cannot be written or permissions cannot be set
     */
    public function toFile(string $file): void
    {
        $result = @file_put_contents($file, (string) $this);

        if ($result === false) {
            throw new FilePermissionException(sprintf('Could not write file "%s"', $file), 1486563789);
        }

        $result = @chmod($file, 0600);

        if ($result === false) {
            throw new FilePermissionException(sprintf('Could not change permissions of file "%s"', $file), 1486563909);
        }
    }

    /**
     * Return all public keys in the file
     *
     * @return KeyInterface[]
     */
    public function getKeys(): array
    {
        $keys = [];

        foreach ($this->keyLines as $line) {
            $keys[] = $this->lines[$line];
        }

        return $keys;
    }

    /**
     * Add a public key to the file
     */
    public function addKey(PublicKey $key): void
    {
        $index = $key->getKey();

        if (!isset($this->keyLines[$index])) {
            $this->keyLines[$index] = count($this->lines);
        }

        $this->lines[$this->keyLines[$index]] = $key;
    }

    /**
     * Remove a public key from the file
     */
    public function removeKey(PublicKey $key): void
    {
        $index = $key->getKey();

        if (isset($this->keyLines[$index])) {
            unset($this->lines[$this->keyLines[$index]], $this->keyLines[$index]);
        }
    }

    /**
     * Returns the file content as string
     */
    public function __toString(): string
    {
        return implode("\n", $this->lines);
    }

    public function getIterator(): \Traversable
    {
        foreach ($this->getKeys() as $key) {
            yield $key;
        }
    }

    /**
     * Parses content of a authorized_keys file
     *
     * @param string $content content of the authorized_keys file
     * @return array
     */
    private function parse(string $content): array
    {
        $lines = explode("\n", $content);
        $lines = array_map('trim', $lines);

        foreach ($lines as $i => $line) {
            if (!empty($line) && $line[0] !== '#') {
                try {
                    $publicKey = new PublicKey($line);
                } catch (InvalidKeyException $e) {
                    $publicKey = new InvalidPublicKey($line, $e);
                }

                $lines[$i] = $publicKey;

                $this->keyLines[$publicKey->getKey()] = $i;
            }
        }

        return $lines;
    }
}

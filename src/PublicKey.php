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

/**
 * Represents a public key
 */
final class PublicKey implements KeyInterface
{
    private string $options = '';

    private string $type = '';

    private string $key = '';

    private string $comment = '';

    private const KEY_PATTERN = '/
      (?<options>[^\s]+\s+)?
      (?<type>
        ecdsa-sha2-nistp256
        |
        ecdsa-sha2-nistp384
        |
        ecdsa-sha2-nistp521
        |
        ssh-dss
        |
        ssh-ed25519
        |
        ssh-rsa
      )
      (?<key>\s+[^\s]+)?
      (?<comment>\s+.+)?
    /x';

    public function __construct(string $key)
    {
        $parts = $this->parse($key);

        foreach (['options', 'type', 'key', 'comment'] as $part) {
            if (isset($parts[$part])) {
                $setter = 'set' . ucfirst($part);

                $this->$setter($parts[$part]);
            }
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
            throw new FilePermissionException(sprintf('Could not read file "%s"', $file), 1678790797);
        }

        return new self($content);
    }

    public function getOptions(): string
    {
        return $this->options;
    }

    public function setOptions(string $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function __toString(): string
    {
        $parts = [];

        if (!empty($this->options)) {
            $parts[] = $this->options;
        }

        $parts[] = $this->type;
        $parts[] = $this->key;

        if (!empty($this->comment)) {
            $parts[] = $this->comment;
        }

        return implode(' ', $parts);
    }

    /**
     * @throws InvalidKeyException if the key is invalid
     */
    private function parse(string $key): array
    {
        preg_match(self::KEY_PATTERN, $key, $parts);
        $parts = array_map('trim', $parts);

        if (empty($parts['type'])) {
            throw new InvalidKeyException('Invalid key type', 1486561051);
        }

        if (empty($parts['key'])) {
            throw new InvalidKeyException('Empty key content', 1486561621);
        }

        return $parts;
    }
}

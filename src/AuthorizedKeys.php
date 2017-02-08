<?php
namespace pagemachine\AuthorizedKeys;

use pagemachine\AuthorizedKeys\Exception\InvalidKeyException;

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

/**
 * Manages the authorized_keys file
 */
class AuthorizedKeys {

  /**
   * Lines of the file
   *
   * @var array
   */
  protected $lines = [];

  /**
   * Map of keys to file lines
   *
   * @var array
   */
  protected $keyLines = [];

  /**
   * @param string $content content of the authorized_keys file
   */
  public function __construct($content = null) {

    if (!empty($content)) {

      $this->lines = $this->parse($content);
    }
  }

  /**
   * Creates a new instance from a file
   *
   * @param string $file path of authorized_keys file
   * @return AuthorizedKeys
   */
  public static function fromFile($file) {

    $content = file_get_contents($file);

    return new static($content);
  }

  /**
   * Writes all content to the filesystem
   *
   * Also ensure that the written file has the recommended permissions,
   * namely only readable and writable to the current user
   *
   * @param string $file path of the authorized_keys file
   */
  public function toFile($file) {

    file_put_contents($file, (string) $this);
    chmod($file, 0600);
  }

  /**
   * Return all public keys in the file
   *
   * @return PublicKey[]
   */
  public function getKeys() {

    $keys = [];

    foreach ($this->keyLines as $line) {

      $keys[] = $this->lines[$line];
    }

    return $keys;
  }

  /**
   * Add a public key to the file
   *
   * @param PublicKey $key a public key
   */
  public function addKey(PublicKey $key) {

    $index = $key->getKey();

    if (!isset($this->keyLines[$index])) {

      $this->keyLines[$index] = count($this->lines);
    }

    $this->lines[$this->keyLines[$index]] = $key;
  }

  /**
   * Remove a public key from the file
   *
   * @param PublicKey $key a public key
   */
  public function removeKey(PublicKey $key) {

    $index = $key->getKey();

    if (isset($this->keyLines[$index])) {

      unset($this->lines[$this->keyLines[$index]], $this->keyLines[$index]);
    }
  }

  /**
   * Returns the file content as string
   *
   * @return string
   */
  public function __toString() {

    return implode("\n", $this->lines);
  }

  /**
   * Parses content of a authorized_keys file
   *
   * @param string $content content of the authorized_keys file
   * @return array
   */
  protected function parse($content) {

    $lines = explode("\n", $content);
    $lines = array_map('trim', $lines);

    foreach ($lines as $i => $line) {

      if (!empty($line) && $line[0] !== '#') {

        try {

          $publicKey = new PublicKey($line);
        } catch (InvalidKeyException $e) {

          throw new InvalidKeyException(sprintf('Invalid key at line %d: %s', $i + 1, $e->getMessage()), 1486561427);
        }

        $lines[$i] = $publicKey;

        $this->keyLines[$publicKey->getKey()] = $i;
      }
    }

    return $lines;
  }
}

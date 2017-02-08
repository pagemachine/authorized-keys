<?php
namespace pagemachine\AuthorizedKeys;

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
 * Represents a public key
 */
class PublicKey {

  /**
   * @var string $type
   */
  protected $type;

  /**
   * @var string $key
   */
  protected $key;

  /**
   * @var string $comment
   */
  protected $comment;

  /**
   * @param string $key public key string
   */
  public function __construct($key) {

    $parts = $this->parse($key);

    foreach (['type', 'key', 'comment'] as $part) {

      if (isset($parts[$part])) {

        $this->$part = $parts[$part];
      }
    }
  }

  /**
   * @return string
   */
  public function getType() {

    return $this->type;
  }

  /**
   * @param string $type
   * @return void
   */
  public function setType($type) {

    $this->type = $type;
  }

  /**
   * @return string
   */
  public function getKey() {

    return $this->key;
  }

  /**
   * @param string $key
   * @return void
   */
  public function setKey($key) {

    $this->key = $key;
  }

  /**
   * @return string
   */
  public function getComment() {

    return $this->comment;
  }

  /**
   * @param string $comment
   * @return void
   */
  public function setComment($comment) {

    $this->comment = $comment;
  }

  /**
   * Returns the file content as string
   *
   * @return string
   */
  public function __toString() {

    $parts = [$this->type, $this->key];

    if (!empty($this->comment)) {

      $parts[] = $this->comment;
    }

    return implode(' ', $parts);
  }

  /**
   * Parses a publie key string
   *
   * @param string $key public key string
   * @return array
   */
  protected function parse($key) {

    static $pattern = '/
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
      \s+
      (?<key>[^\s]+)
      (?<comment>.+)?
    /x';
    $parts = [];

    preg_match($pattern, $key, $parts);
    $parts = array_map('trim', $parts);

    return $parts;
  }
}

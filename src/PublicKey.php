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
   * Key string
   *
   * @var string
   */
  protected $key;

  /**
   * @param string $key public key string
   */
  public function __construct($key) {

    $this->key = $key;
  }

  /**
   * Returns the file content as string
   *
   * @return string
   */
  public function __toString() {

    return $this->key;
  }
}

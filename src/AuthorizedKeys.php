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
   * @param string $content content of the authorized_keys file
   */
  public function __construct($content = null) {

    if (!empty($content)) {

      $this->lines = $this->parse($content);
    }
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

    return $lines;
  }

  /**
   * Returns the file content as string
   *
   * @return string
   */
  public function __toString() {

    return implode("\n", $this->lines);
  }
}

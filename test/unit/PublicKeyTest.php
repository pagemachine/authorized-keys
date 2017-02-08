<?php
namespace pagemachine\AuthorizedKeys\Test;

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

use PHPUnit\Framework\TestCase;
use pagemachine\AuthorizedKeys\PublicKey;

/**
 * Testcase for pagemachine\AuthorizedKeys\PublicKey
 */
class PublicKeyTest extends TestCase {

  /**
   * @test
   * @dataProvider keys
   *
   * @param string $key
   */
  public function constructsFromString($key) {

    $publicKey = new PublicKey($key);

    $this->assertEquals($key, (string) $publicKey);
  }

  /**
   * @return array
   */
  public function keys() {

    $options = 'command="/bin/test"';
    $type = 'ssh-rsa';
    $key = 'AAAAB3NzaC1yc2EAAAADAQABAAABAQDFy1wC52dQBLnJ8dwQCsTwTuDwCQAhb/2joe6oK4Qm6XBI89BerXTsTvV8ekxg3LjvD6LjclJR6WsDQPA8cJeKXl/XDtcd+a355fth1sRZwe20Zh7NrpfhGD8Pb4HWrnJz0jeVXn5M/FppvRFl4RX7dhz5zuHFIb8BeCOmoNid1vTucp9HCr9PkCcahRpw4QXU5v2ETXbbxmftGz7PBvGHR2In1nm3MBBlX++11sDhlYUCWqJXjfH0dvgpWvEtknJoyHjX8MvNV6oXeh59ow6unIOJjXPkdyICXjZCJtBdVK2pc3mYKxaDWNN7MvLelduw941CXaa4aE2EFDa0BLTJ';
    $comment = 'mbrodala@pagemachine.de';

    return [
      'minimum' => [implode(' ', [$type, $key])],
      'with comment' => [implode(' ', [$type, $key, $comment])],
      'with options' => [implode(' ', [$options, $type, $key])],
      'with options and comment' => [implode(' ', [$options, $type, $key, $comment])],
    ];
  }

  /**
   * @test
   */
  public function parsesKeyParts() {

    $key = <<<FILE
command="/bin/test" ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQDFy1wC52dQBLnJ8dwQCsTwTuDwCQAhb/2joe6oK4Qm6XBI89BerXTsTvV8ekxg3LjvD6LjclJR6WsDQPA8cJeKXl/XDtcd+a355fth1sRZwe20Zh7NrpfhGD8Pb4HWrnJz0jeVXn5M/FppvRFl4RX7dhz5zuHFIb8BeCOmoNid1vTucp9HCr9PkCcahRpw4QXU5v2ETXbbxmftGz7PBvGHR2In1nm3MBBlX++11sDhlYUCWqJXjfH0dvgpWvEtknJoyHjX8MvNV6oXeh59ow6unIOJjXPkdyICXjZCJtBdVK2pc3mYKxaDWNN7MvLelduw941CXaa4aE2EFDa0BLTJ mbrodala@pagemachine.de
FILE;

    $publicKey = new PublicKey($key);

    $this->assertEquals('command="/bin/test"', $publicKey->getOptions());
    $this->assertEquals('ssh-rsa', $publicKey->getType());
    $this->assertEquals('AAAAB3NzaC1yc2EAAAADAQABAAABAQDFy1wC52dQBLnJ8dwQCsTwTuDwCQAhb/2joe6oK4Qm6XBI89BerXTsTvV8ekxg3LjvD6LjclJR6WsDQPA8cJeKXl/XDtcd+a355fth1sRZwe20Zh7NrpfhGD8Pb4HWrnJz0jeVXn5M/FppvRFl4RX7dhz5zuHFIb8BeCOmoNid1vTucp9HCr9PkCcahRpw4QXU5v2ETXbbxmftGz7PBvGHR2In1nm3MBBlX++11sDhlYUCWqJXjfH0dvgpWvEtknJoyHjX8MvNV6oXeh59ow6unIOJjXPkdyICXjZCJtBdVK2pc3mYKxaDWNN7MvLelduw941CXaa4aE2EFDa0BLTJ', $publicKey->getKey());
    $this->assertEquals('mbrodala@pagemachine.de', $publicKey->getComment());
  }
}

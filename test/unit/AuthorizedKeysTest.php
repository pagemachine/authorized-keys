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
use org\bovigo\vfs\vfsStream;
use pagemachine\AuthorizedKeys\AuthorizedKeys;
use pagemachine\AuthorizedKeys\PublicKey;

/**
 * Testcase for pagemachine\AuthorizedKeys\AuthorizedKeys
 */
class AuthorizedKeysTest extends TestCase {

  /**
   * @test
   */
  public function constructsFromString() {

    $content = <<<FILE
ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQDFy1wC52dQBLnJ8dwQCsTwTuDwCQAhb/2joe6oK4Qm6XBI89BerXTsTvV8ekxg3LjvD6LjclJR6WsDQPA8cJeKXl/XDtcd+a355fth1sRZwe20Zh7NrpfhGD8Pb4HWrnJz0jeVXn5M/FppvRFl4RX7dhz5zuHFIb8BeCOmoNid1vTucp9HCr9PkCcahRpw4QXU5v2ETXbbxmftGz7PBvGHR2In1nm3MBBlX++11sDhlYUCWqJXjfH0dvgpWvEtknJoyHjX8MvNV6oXeh59ow6unIOJjXPkdyICXjZCJtBdVK2pc3mYKxaDWNN7MvLelduw941CXaa4aE2EFDa0BLTJ mbrodala@pagemachine.de
FILE;

    $authorizedKeys = new AuthorizedKeys($content);

    $this->assertEquals($content, (string) $authorizedKeys);
  }

  /**
   * @test
   */
  public function constructsFromFile() {

    $content = <<<FILE
ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQDFy1wC52dQBLnJ8dwQCsTwTuDwCQAhb/2joe6oK4Qm6XBI89BerXTsTvV8ekxg3LjvD6LjclJR6WsDQPA8cJeKXl/XDtcd+a355fth1sRZwe20Zh7NrpfhGD8Pb4HWrnJz0jeVXn5M/FppvRFl4RX7dhz5zuHFIb8BeCOmoNid1vTucp9HCr9PkCcahRpw4QXU5v2ETXbbxmftGz7PBvGHR2In1nm3MBBlX++11sDhlYUCWqJXjfH0dvgpWvEtknJoyHjX8MvNV6oXeh59ow6unIOJjXPkdyICXjZCJtBdVK2pc3mYKxaDWNN7MvLelduw941CXaa4aE2EFDa0BLTJ mbrodala@pagemachine.de
FILE;

    $directory = vfsStream::setup();
    $file = vfsStream::newFile('authorized_keys')
      ->withContent($content)
      ->at($directory);

    $authorizedKeys = AuthorizedKeys::fromFile($file->url());

    $this->assertEquals($content, (string) $authorizedKeys);
  }

  /**
   * @test
   */
  public function addsKeys() {

    $authorizedKeys = new AuthorizedKeys('');

    $firstKey = new PublicKey('ssh-rsa AAA first');
    $authorizedKeys->addKey($firstKey);

    $secondKey = new PublicKey('ssh-rsa BBB second');
    $authorizedKeys->addKey($secondKey);

    $expected = <<<FILE
ssh-rsa AAA first
ssh-rsa BBB second
FILE;

    $this->assertEquals($expected, (string) $authorizedKeys);
  }
}

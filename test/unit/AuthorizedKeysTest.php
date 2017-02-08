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
ssh-rsa AAA test
FILE;

    $authorizedKeys = new AuthorizedKeys($content);

    $this->assertEquals($content, (string) $authorizedKeys);
  }

  /**
   * @test
   */
  public function constructsFromFile() {

    $content = <<<FILE
ssh-rsa AAA test
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

  /**
   * @test
   */
  public function removesKeys() {

    $content = <<<FILE
ssh-rsa AAA first
ssh-rsa BBB second
FILE;

    $authorizedKeys = new AuthorizedKeys($content);
    $publicKey = new PublicKey('ssh-rsa AAA');

    $authorizedKeys->removeKey($publicKey);

    $expected = <<<FILE
ssh-rsa BBB second
FILE;

    $this->assertEquals('ssh-rsa BBB second', (string) $authorizedKeys);
  }

  /**
   * @test
   */
  public function removesKeysOnce() {

    $content = <<<FILE
ssh-rsa AAA first
ssh-rsa BBB second
FILE;

    $authorizedKeys = new AuthorizedKeys($content);
    $publicKey = new PublicKey('ssh-rsa AAA');

    $authorizedKeys->removeKey($publicKey);
    $authorizedKeys->removeKey($publicKey);

    $expected = <<<FILE
ssh-rsa BBB second
FILE;

    $this->assertEquals('ssh-rsa BBB second', (string) $authorizedKeys);
  }
}

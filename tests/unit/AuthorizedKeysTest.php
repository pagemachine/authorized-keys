<?php
namespace Pagemachine\AuthorizedKeys\Test;

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
use Pagemachine\AuthorizedKeys\AuthorizedKeys;
use Pagemachine\AuthorizedKeys\Exception\FilePermissionException;
use Pagemachine\AuthorizedKeys\Exception\InvalidKeyException;
use Pagemachine\AuthorizedKeys\PublicKey;

/**
 * Testcase for pagemachine\AuthorizedKeys\AuthorizedKeys
 */
class AuthorizedKeysTest extends TestCase
{
    /**
     * @test
     */
    public function constructsFromString()
    {
        $content = <<<FILE
ssh-rsa AAA test
FILE;

        $authorizedKeys = new AuthorizedKeys($content);

        $this->assertEquals($content, (string) $authorizedKeys);
    }

    /**
     * @test
     */
    public function constructsFromFile()
    {
        $content = <<<FILE
# A comment
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
    public function throwsExceptionOnFileReadError()
    {
        $directory = vfsStream::setup();
        $file = vfsStream::newFile('authorized_keys')
        ->chmod(0)
        ->at($directory);

        $this->expectException(FilePermissionException::class);

        AuthorizedKeys::fromFile($file->url());
    }

    /**
     * @test
     */
    public function writesToFile()
    {
        $content = <<<FILE
ssh-rsa AAA test
FILE;

        $authorizedKeys = new AuthorizedKeys($content);

        $directory = vfsStream::setup();
        $file = vfsStream::newFile('authorized_keys')
        ->at($directory);

        $authorizedKeys->toFile($file->url());

        $this->assertTrue($directory->hasChild('authorized_keys'));
        $this->assertEquals($content, $file->getContent());
        $this->assertTrue($file->isReadable($file->getUser(), $file->getGroup()), 'File should be readable by the owner');
        $this->assertTrue($file->isWritable($file->getUser(), $file->getGroup()), 'File should be writable by the owner');
        $this->assertFalse($file->isReadable('other', 'other'), 'File should not be readable by others');
        $this->assertFalse($file->isWritable('other', 'other'), 'File should not be writable by others');
    }

    /**
     * @test
     */
    public function throwsExceptionOnFileWriteError()
    {
        $content = <<<FILE
ssh-rsa AAA test
FILE;

        $authorizedKeys = new AuthorizedKeys($content);

        $directory = vfsStream::setup();
        $file = vfsStream::newFile('authorized_keys')
        ->chmod(0)
        ->at($directory);

        $this->expectException(FilePermissionException::class);
        $this->expectExceptionCode(1486563789);

        $authorizedKeys->toFile($file->url());
    }

    /**
     * @test
     */
    public function throwsExceptionOnFilePermissionFixError()
    {
        $content = <<<FILE
ssh-rsa AAA test
FILE;

        $authorizedKeys = new AuthorizedKeys($content);

        $directory = vfsStream::setup();
        $file = vfsStream::newFile('authorized_keys')
        ->chown(1)
        ->at($directory);

        $this->expectException(FilePermissionException::class);
        $this->expectExceptionCode(1486563909);

        $authorizedKeys->toFile($file->url());
    }

    /**
     * @test
     */
    public function getsKeys()
    {
        $content = <<<FILE
# A comment
ssh-rsa AAA first

ssh-rsa BBB second
FILE;

        $authorizedKeys = new AuthorizedKeys($content);

        $keys = $authorizedKeys->getKeys();

        $this->assertCount(2, $keys);
        $this->assertContainsOnlyInstancesOf(PublicKey::class, $keys);
    }

    /**
     * @test
     */
    public function getsKeysByIteration()
    {
        $content = <<<FILE
# A comment
ssh-rsa AAA first

ssh-rsa BBB second
FILE;

        $authorizedKeys = new AuthorizedKeys($content);

        $keys = [];

        foreach ($authorizedKeys as $key) {
            $keys[] = $key;
        }

        $this->assertCount(2, $keys);
        $this->assertContainsOnlyInstancesOf(PublicKey::class, $keys);
    }

    /**
     * @test
     */
    public function addsKeys()
    {
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
    public function addsKeysOnce()
    {
        $authorizedKeys = new AuthorizedKeys('');

        $publicKey = new PublicKey('ssh-rsa AAA test');

        $authorizedKeys->addKey($publicKey);
        $authorizedKeys->addKey($publicKey);

        $expected = <<<FILE
ssh-rsa AAA test
FILE;

        $this->assertEquals($expected, (string) $authorizedKeys);
    }

    /**
     * @test
     */
    public function addsKeysAvoidsMissingTrailingNewline()
    {
        $authorizedKeys = new AuthorizedKeys('ssh-rsa AAA first');

        $publicKey = new PublicKey('ssh-rsa BBB second');

        $authorizedKeys->addKey($publicKey);

        $expected = <<<FILE
ssh-rsa AAA first
ssh-rsa BBB second
FILE;

        $this->assertEquals($expected, (string) $authorizedKeys);
    }

    /**
     * @test
     */
    public function removesKeys()
    {
        $content = <<<FILE
# A comment
ssh-rsa AAA first
ssh-rsa BBB second
FILE;

        $authorizedKeys = new AuthorizedKeys($content);
        $firstKey = new PublicKey('ssh-rsa AAA');

        $authorizedKeys->removeKey($firstKey);

        $expected = <<<FILE
# A comment
ssh-rsa BBB second
FILE;

        $this->assertEquals($expected, (string) $authorizedKeys);
    }

    /**
     * @test
     */
    public function removesKeysOnce()
    {
        $content = <<<FILE
ssh-rsa AAA first
ssh-rsa BBB second
FILE;

        $authorizedKeys = new AuthorizedKeys($content);
        $firstKey = new PublicKey('ssh-rsa AAA');

        $authorizedKeys->removeKey($firstKey);
        $authorizedKeys->removeKey($firstKey);

        $expected = <<<FILE
ssh-rsa BBB second
FILE;

        $this->assertEquals($expected, (string) $authorizedKeys);
    }

    /**
     * @test
     */
    public function throwsExceptionOnInvalidKeys()
    {
        $content = <<<FILE
ssh-rsa AAA first
foo BBB second
FILE;

        $this->expectException(InvalidKeyException::class);
        $this->expectExceptionMessageRegExp('/Invalid key at line 2: .+/');

        new AuthorizedKeys($content);
    }
}

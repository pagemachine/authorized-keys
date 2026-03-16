<?php

declare(strict_types=1);

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
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use Pagemachine\AuthorizedKeys\Exception\InvalidKeyException;
use Pagemachine\AuthorizedKeys\PublicKey;
use PHPUnit\Framework\TestCase;

/**
 * Testcase for pagemachine\AuthorizedKeys\PublicKey
 */
final class PublicKeyTest extends TestCase
{
    /**
     *
     * @param string $key
     */
    #[Test]
    #[DataProvider('keys')]
    public function constructsFromString($key)
    {
        $publicKey = new PublicKey($key);

        $this->assertEquals($key, (string) $publicKey);
    }

    public static function keys(): \Iterator
    {
        yield 'minimum' => ['ssh-rsa AAA'];
        yield 'with comment' => ['ssh-rsa AAA test'];
        yield 'with options' => ['command="/bin/test" ssh-rsa AAA'];
        yield 'with options and comment' => ['command="/bin/test" ssh-rsa AAA test'];
    }

    #[Test]
    public function parsesKeyParts()
    {
        $key = <<<FILE
            command="/bin/test" ssh-rsa AAA test
            FILE;

        $publicKey = new PublicKey($key);

        $this->assertSame('command="/bin/test"', $publicKey->getOptions());
        $this->assertSame('ssh-rsa', $publicKey->getType());
        $this->assertSame('AAA', $publicKey->getKey());
        $this->assertSame('test', $publicKey->getComment());
    }

    #[Test]
    public function setsKeyParts()
    {
        $key = <<<FILE
            command="/bin/test" ssh-rsa AAA test
            FILE;

        $publicKey = new PublicKey($key);

        $publicKey->setOptions('agent-forwarding');
        $this->assertSame('agent-forwarding', $publicKey->getOptions());

        $publicKey->setType('ssh-dss');
        $this->assertSame('ssh-dss', $publicKey->getType());

        $publicKey->setKey('BBB');
        $this->assertSame('BBB', $publicKey->getKey());

        $publicKey->setComment('foo');
        $this->assertSame('foo', $publicKey->getComment());

        $this->assertSame('agent-forwarding ssh-dss BBB foo', (string) $publicKey);
    }

    #[Test]
    public function throwsExceptionOnInvalidType()
    {
        $this->expectException(InvalidKeyException::class);
        $this->expectExceptionCode(1486561051);

        new PublicKey('foo AAA');
    }

    #[Test]
    public function throwsExceptionOnEmptyKey()
    {
        $this->expectException(InvalidKeyException::class);
        $this->expectExceptionCode(1486561621);

        new PublicKey('ssh-rsa');
    }
}

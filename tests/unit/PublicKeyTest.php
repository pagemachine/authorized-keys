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

use Pagemachine\AuthorizedKeys\Exception\InvalidKeyException;
use Pagemachine\AuthorizedKeys\PublicKey;
use PHPUnit\Framework\TestCase;

/**
 * Testcase for pagemachine\AuthorizedKeys\PublicKey
 */
final class PublicKeyTest extends TestCase
{
    /**
     * @test
     * @dataProvider keys
     *
     * @param string $key
     */
    public function constructsFromString($key)
    {
        $publicKey = new PublicKey($key);

        $this->assertEquals($key, (string) $publicKey);
    }

    /**
     * @return array
     */
    public function keys(): array
    {
        return [
            'minimum' => ['ssh-rsa AAA'],
            'with comment' => ['ssh-rsa AAA test'],
            'with options' => ['command="/bin/test" ssh-rsa AAA'],
            'with options and comment' => ['command="/bin/test" ssh-rsa AAA test'],
        ];
    }

    /**
     * @test
     */
    public function parsesKeyParts()
    {
        $key = <<<FILE
            command="/bin/test" ssh-rsa AAA test
            FILE;

        $publicKey = new PublicKey($key);

        $this->assertEquals('command="/bin/test"', $publicKey->getOptions());
        $this->assertEquals('ssh-rsa', $publicKey->getType());
        $this->assertEquals('AAA', $publicKey->getKey());
        $this->assertEquals('test', $publicKey->getComment());
    }

    /**
     * @test
     */
    public function setsKeyParts()
    {
        $key = <<<FILE
            command="/bin/test" ssh-rsa AAA test
            FILE;

        $publicKey = new PublicKey($key);

        $publicKey->setOptions('agent-forwarding');
        $this->assertEquals('agent-forwarding', $publicKey->getOptions());

        $publicKey->setType('ssh-dss');
        $this->assertEquals('ssh-dss', $publicKey->getType());

        $publicKey->setKey('BBB');
        $this->assertEquals('BBB', $publicKey->getKey());

        $publicKey->setComment('foo');
        $this->assertEquals('foo', $publicKey->getComment());

        $this->assertEquals('agent-forwarding ssh-dss BBB foo', (string) $publicKey);
    }

    /**
     * @test
     */
    public function throwsExceptionOnInvalidType()
    {
        $this->expectException(InvalidKeyException::class);
        $this->expectExceptionCode(1486561051);

        new PublicKey('foo AAA');
    }

    /**
     * @test
     */
    public function throwsExceptionOnEmptyKey()
    {
        $this->expectException(InvalidKeyException::class);
        $this->expectExceptionCode(1486561621);

        new PublicKey('ssh-rsa');
    }
}

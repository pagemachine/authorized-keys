<?php

declare(strict_types = 1);

namespace Pagemachine\AuthorizedKeys;

use Pagemachine\AuthorizedKeys\Exception\InvalidKeyException;

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
 * An invalid public key
 */
final class InvalidPublicKey implements KeyInterface
{
    /**
     * @var string
     */
    protected $key = '';

    /**
     * @var InvalidKeyException
     */
    protected $error;

    /**
     * @param string $key public key string
     * @param InvalidKeyException $error error with the key string
     */
    public function __construct(string $key, InvalidKeyException $error)
    {
        $this->key = $key;
        $this->error = $error;
    }

    /**
     * @return string
     */
    public function getOptions(): string
    {
        return '';
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return '';
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return '';
    }

    /**
     * Returns the file content as string
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->key;
    }

    /**
     * @return InvalidKeyException
     */
    public function getError(): InvalidKeyException
    {
        return $this->error;
    }
}

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
class InvalidPublicKey extends PublicKey
{
    /**
     * @var string
     */
    protected $rawKey = '';

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
        $this->rawKey = $key;
        $this->error = $error;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->rawKey;
    }

    /**
     * @return InvalidKeyException
     */
    public function getError(): InvalidKeyException
    {
        return $this->error;
    }

    /**
     * Returns the file content as string
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->rawKey;
    }
}

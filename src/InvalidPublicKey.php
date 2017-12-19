<?php
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

class InvalidPublicKey extends PublicKey
{
    /**
     * @var string
     */
    protected $rawKey;

    /**
     * @var InvalidKeyException
     */
    protected $error;

    /**
     * @param string $key public key string
     */
    public function __construct($key)
    {
        $this->rawKey = $key;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->rawKey;
    }

    /**
     * Returns the file content as string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->rawKey;
    }

    /**
     * @return InvalidKeyException
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param InvalidKeyException $error
     * @return void
     */
    public function setError(InvalidKeyException $error)
    {
        $this->error = $error;
    }
}

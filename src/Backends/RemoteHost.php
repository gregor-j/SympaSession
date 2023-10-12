<?php

declare(strict_types=1);

namespace GregorJ\SympaSession\Backends;

use GregorJ\SympaSession\DataTypes\RemoteAddress;
use GregorJ\SympaSession\Exceptions\InvalidRemoteAddressException;
use GregorJ\SympaSession\Exceptions\NoRemoteHostException;

/**
 *
 */
final class RemoteHost
{
    private static string $key = 'REMOTE_ADDR';

    /**
     * @return RemoteAddress
     * @throws InvalidRemoteAddressException
     * @throws NoRemoteHostException
     */
    public static function get(): RemoteAddress
    {
        $remoteHost = getenv(self::$key, true) ?: getenv(self::$key);
        if (!is_string($remoteHost) || $remoteHost === '') {
            throw new NoRemoteHostException(sprintf('Remote host key \'%s\' is empty!', self::$key));
        }
        return new RemoteAddress($remoteHost);
    }

    /**
     * @param string $key
     * @return void
     */
    public static function setRemoteHostKey(string $key): void
    {
        self::$key = $key;
    }
}

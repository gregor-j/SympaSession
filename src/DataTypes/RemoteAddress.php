<?php

declare(strict_types=1);

namespace GregorJ\SympaSession\DataTypes;

use GregorJ\SympaSession\Exceptions\InvalidRemoteAddressException;

/**
 *
 */
final class RemoteAddress
{
    private string $remoteAddress;

    /**
     * @param string $remoteAddress
     * @throws InvalidRemoteAddressException
     */
    public function __construct(string $remoteAddress)
    {
        if (filter_var($remoteAddress, FILTER_VALIDATE_IP) === false) {
            throw new InvalidRemoteAddressException(sprintf('Invalid remote address \'%s\'!', $remoteAddress));
        }
        $this->remoteAddress = $remoteAddress;
    }

    /**
     * @param RemoteAddress $remoteAddress
     * @return bool
     */
    public function equals(RemoteAddress $remoteAddress): bool
    {
        return (string)$remoteAddress === $this->remoteAddress;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->remoteAddress;
    }
}

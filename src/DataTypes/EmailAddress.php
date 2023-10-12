<?php

declare(strict_types=1);

namespace GregorJ\SympaSession\DataTypes;

use GregorJ\SympaSession\Exceptions\InvalidEmailException;

/**
 * A validated and canonized email address.
 */
final class EmailAddress
{
    private string $emailAddress;

    /**
     * @param string $emailAddress
     * @throws InvalidEmailException
     */
    public function __construct(string $emailAddress)
    {
        if (filter_var($emailAddress, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidEmailException(sprintf('Invalid email address \'%s\'!', $emailAddress));
        }
        $this->emailAddress = strtolower(filter_var($emailAddress, FILTER_SANITIZE_EMAIL));
    }

    /**
     * @param EmailAddress $emailAddress
     * @return bool
     */
    public function equals(EmailAddress $emailAddress): bool
    {
        return (string)$emailAddress === $this->emailAddress;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->emailAddress;
    }
}

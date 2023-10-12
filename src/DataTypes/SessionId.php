<?php

declare(strict_types=1);

namespace GregorJ\SympaSession\DataTypes;

use GregorJ\SympaSession\Exceptions\InvalidSessionIdException;

/**
 *
 */
final class SessionId
{
    private string $id;

    /**
     * @param string|null $id
     * @throws InvalidSessionIdException
     */
    public function __construct(string $id = null)
    {
        if ($id === null) {
            //10000001000000 - 99999999999999
            $id = rand(1000000, 9999999) . rand(1000000, 9999999);
        }
        $this->setId($id);
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return void
     * @throws InvalidSessionIdException
     */
    public function setId(string $id): void
    {
        //10000000000000 - 99999999999999
        if (!preg_match('~^[1-9][0-9]{13}$~', $id)) {
            throw new InvalidSessionIdException(sprintf('Invalid session ID \'%s\'', $id));
        }
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->id;
    }
}

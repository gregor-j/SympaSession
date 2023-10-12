<?php

declare(strict_types=1);

namespace GregorJ\SympaSession\Interfaces;

use GregorJ\SympaSession\DataTypes\SessionId;
use GregorJ\SympaSession\Entities\Session;
use GregorJ\SympaSession\Exceptions\DatabaseConnectionException;
use GregorJ\SympaSession\Exceptions\InvalidSessionPropertyException;
use GregorJ\SympaSession\Exceptions\MissingSessionPropertyException;
use GregorJ\SympaSession\Exceptions\SessionIdNotFoundException;

/**
 * The database interface.
 */
interface DatabaseInterface
{
    /**
     * Creates a new session.
     * @param Session $session
     * @return void
     * @throws DatabaseConnectionException in case the DB connection fails
     * @throws MissingSessionPropertyException
     */
    public function createSession(Session $session): void;

    /**
     * Load the session with the given SessionId from the database,
     * or returns null in any other case.
     * @param SessionId $id
     * @return Session
     * @throws SessionIdNotFoundException in case the session ID cannot be found in the DB
     * @throws InvalidSessionPropertyException in case one of the datatype classes causes an exception while reading
     * @throws DatabaseConnectionException in case the DB connection fails
     */
    public function loadSession(SessionId $id): Session;

    /**
     * Updates an existing session.
     * @param Session $session
     * @return void
     * @throws DatabaseConnectionException in case the DB connection fails
     * @throws InvalidSessionPropertyException
     * @throws MissingSessionPropertyException
     */
    public function updateSession(Session $session): void;
}

<?php

declare(strict_types=1);

namespace GregorJ\SympaSession;

use GregorJ\SympaSession\Backends\RemoteHost;
use GregorJ\SympaSession\Backends\SympaCookie;
use GregorJ\SympaSession\DataTypes\EmailAddress;
use GregorJ\SympaSession\DataTypes\SessionData;
use GregorJ\SympaSession\DataTypes\SessionId;
use GregorJ\SympaSession\DataTypes\SympaRobot;
use GregorJ\SympaSession\Entities\Session;
use GregorJ\SympaSession\Exceptions\DatabaseConnectionException;
use GregorJ\SympaSession\Exceptions\InvalidRemoteAddressException;
use GregorJ\SympaSession\Exceptions\InvalidSessionIdException;
use GregorJ\SympaSession\Exceptions\InvalidSessionPropertyException;
use GregorJ\SympaSession\Exceptions\MissingCookieException;
use GregorJ\SympaSession\Exceptions\MissingSessionPropertyException;
use GregorJ\SympaSession\Exceptions\NoRemoteHostException;
use GregorJ\SympaSession\Exceptions\SessionIdMissingException;
use GregorJ\SympaSession\Exceptions\SessionIdNotFoundException;
use GregorJ\SympaSession\Exceptions\SetCookieException;
use GregorJ\SympaSession\Interfaces\DatabaseInterface;

/**
 * Sympa session class
 */
final class SympaSession
{
    private static DatabaseInterface $database;

    private static SympaRobot $robot;

    /**
     * @param DatabaseInterface $database
     * @return void
     */
    public static function setDatabase(DatabaseInterface $database): void
    {
        self::$database = $database;
    }

    /**
     * @param SympaRobot $robot
     * @return void
     */
    public static function setRobot(SympaRobot $robot): void
    {
        self::$robot = $robot;
    }

    /**
     * @param EmailAddress $email
     * @throws DatabaseConnectionException
     * @throws MissingSessionPropertyException
     * @throws SessionIdMissingException
     * @throws SetCookieException
     */
    public function __construct(EmailAddress $email)
    {
        $session = $this->getOrCreateSession($email);
        SympaCookie::setCookie($session->getSessionId(), self::$robot);
    }

    /**
     * @param EmailAddress $email
     * @return Session
     * @throws DatabaseConnectionException
     * @throws MissingSessionPropertyException
     */
    public function getOrCreateSession(EmailAddress $email): Session
    {
        if ($session = $this->getSession($this->getSessionId())) {
            if (
                $this->sameRemoteHost($session)
                && $email->equals($session->getEmail())
            ) {
                return $this->updateSession($session);
            }
            trigger_error(
                sprintf(
                    'Found session from \'%s\' of a different user \'%s\'.',
                    (string)$session->getRemoteAddress(),
                    (string)$email
                ),
                E_USER_NOTICE
            );
        }
        return $this->createSession($email);
    }

    /**
     * @return SessionId|null
     */
    public function getSessionId(): ?SessionId
    {
        if (!SympaCookie::isCookieSet()) {
            return null;
        }
        try {
            return SympaCookie::getSessionId();
        } catch (MissingCookieException $exception) {
            trigger_error($exception->getMessage(), E_USER_NOTICE);
        } catch (InvalidSessionIdException $exception) {
            trigger_error($exception->getMessage(), E_USER_WARNING);
        }
        return null;
    }

    /**
     * @param SessionId|null $sessionId
     * @return Session|null
     * @throws DatabaseConnectionException
     */
    public function getSession(?SessionId $sessionId = null): ?Session
    {
        if ($sessionId === null) {
            return null;
        }
        try {
            return self::$database->loadSession($sessionId);
        } catch (SessionIdNotFoundException $exception) {
            trigger_error($exception->getMessage(), E_USER_NOTICE);
        } catch (InvalidSessionPropertyException $exception) {
            trigger_error($exception->getMessage(), E_USER_ERROR);
        }
        return null;
    }

    /**
     * @param Session $session
     * @return bool
     */
    public function sameRemoteHost(Session $session): bool
    {
        try {
            return RemoteHost::get()->equals($session->getRemoteAddress());
        } catch (NoRemoteHostException | InvalidRemoteAddressException $exception) {
            trigger_error($exception->getMessage(), E_USER_WARNING);
            return false;
        }
    }

    /**
     * @param Session $session
     * @return Session
     * @throws DatabaseConnectionException
     */
    public function updateSession(Session $session): Session
    {
        if (!$session->getData()->isAuthClassic()) {
            $session->getData()->set(SessionData::KEY_AUTH, SessionData::AUTH_CLASSIC);
            try {
                self::$database->updateSession($session);
            } catch (MissingSessionPropertyException | InvalidSessionPropertyException $exception) {
                trigger_error($exception->getMessage(), E_USER_ERROR);
            }
        }
        return $session;
    }

    /**
     * @param EmailAddress $email
     * @return Session
     * @throws DatabaseConnectionException
     * @throws MissingSessionPropertyException
     */
    public function createSession(EmailAddress $email): Session
    {
        $session = new Session();
        $session->setSessionId(new SessionId());
        $session->setEmail($email);
        self::$database->createSession($session);
        return $session;
    }
}

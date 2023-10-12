<?php

declare(strict_types=1);

namespace GregorJ\SympaSession\Backends;

use GregorJ\SympaSession\DataTypes\SessionId;
use GregorJ\SympaSession\DataTypes\SympaRobot;
use GregorJ\SympaSession\Exceptions\InvalidSessionIdException;
use GregorJ\SympaSession\Exceptions\MissingCookieException;
use GregorJ\SympaSession\Exceptions\SetCookieException;

/**
 *
 */
final class SympaCookie
{
    public const COOKIE_NAME = 'sympa_session';

    /**
     * @return bool
     */
    public static function isCookieSet(): bool
    {
        return isset($_COOKIE[self::COOKIE_NAME]);
    }

    /**
     * @return SessionId
     * @throws MissingCookieException
     * @throws InvalidSessionIdException
     */
    public static function getSessionId(): SessionId
    {
        if (!self::isCookieSet()) {
            throw new MissingCookieException(sprintf('Missing cookie \'%s\'!', self::COOKIE_NAME));
        }
        return new SessionId($_COOKIE[self::COOKIE_NAME]);
    }

    /**
     * @param SessionId $sessionId
     * @param SympaRobot $robot
     * @return void
     * @throws SetCookieException
     */
    public static function setCookie(SessionId $sessionId, SympaRobot $robot): void
    {
        $_COOKIE[self::COOKIE_NAME] = $sessionId->getId();
        $success = setcookie(
            self::COOKIE_NAME,
            $sessionId->getId(),
            0,
            $robot->getPath(),
            $robot->getHost(),
            false
        );
        if (!$success) {
            throw new SetCookieException('Failed to set cookie! Output prior to calling setcookie() exists.');
        }
    }
}

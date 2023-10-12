<?php

declare(strict_types=1);

namespace GregorJ\SympaSession\Entities;

use DateTime;
use GregorJ\SympaSession\DataTypes\EmailAddress;
use GregorJ\SympaSession\DataTypes\RemoteAddress;
use GregorJ\SympaSession\DataTypes\SessionData;
use GregorJ\SympaSession\DataTypes\SessionId;
use GregorJ\SympaSession\DataTypes\SympaRobot;
use GregorJ\SympaSession\Exceptions\SessionIdMissingException;

/**
 *
 */
final class Session
{
    private SessionData $data;

    private DateTime $date;

    private EmailAddress $email;

    private int $hits;

    private ?SessionId $sessionId = null;

    private ?RemoteAddress $remoteAddress = null;

    private ?SympaRobot $robot = null;

    private DateTime $startDate;

    public function __construct()
    {
        $this->setDate(new DateTime());
        $this->setStartDate(new DateTime());
        $this->setHits(1);
        $this->setData(new SessionData());
    }

    /**
     * @return SessionData
     */
    public function getData(): SessionData
    {
        return $this->data;
    }

    /**
     * @param SessionData $data
     * @return void
     */
    public function setData(SessionData $data): void
    {
        $this->data = $data;
    }

    /**
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     * @return void
     */
    public function setDate(DateTime $date): void
    {
        $this->date = $date;
    }

    /**
     * @return EmailAddress
     */
    public function getEmail(): EmailAddress
    {
        return $this->email;
    }

    /**
     * @param EmailAddress $email
     * @return void
     */
    public function setEmail(EmailAddress $email): void
    {
        $this->email = $email;
    }

    /**
     * @return int
     */
    public function getHits(): int
    {
        return $this->hits;
    }

    /**
     * @param int $hits
     * @return void
     */
    public function setHits(int $hits): void
    {
        $this->hits = $hits;
    }

    /**
     * @return SessionId
     * @throws SessionIdMissingException
     */
    public function getSessionId(): SessionId
    {
        if ($this->sessionId === null) {
            throw new SessionIdMissingException('The session ID is missing!');
        }
        return $this->sessionId;
    }

    /**
     * @param SessionId $sessionId
     * @return void
     */
    public function setSessionId(SessionId $sessionId): void
    {
        $this->sessionId = $sessionId;
    }

    /**
     * @return RemoteAddress
     */
    public function getRemoteAddress(): RemoteAddress
    {
        return $this->remoteAddress;
    }

    /**
     * @param RemoteAddress $remoteAddress
     * @return void
     */
    public function setRemoteAddress(RemoteAddress $remoteAddress): void
    {
        $this->remoteAddress = $remoteAddress;
    }

    /**
     * @return SympaRobot
     */
    public function getRobot(): SympaRobot
    {
        return $this->robot;
    }

    /**
     * @param SympaRobot $robot
     * @return void
     */
    public function setRobot(SympaRobot $robot): void
    {
        $this->robot = $robot;
    }

    /**
     * @return DateTime|null
     */
    public function getStartDate(): ?DateTime
    {
        return $this->startDate;
    }

    /**
     * @param DateTime $startDate
     * @return void
     */
    public function setStartDate(DateTime $startDate): void
    {
        $this->startDate = $startDate;
    }
}

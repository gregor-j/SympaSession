<?php

declare(strict_types=1);

namespace GregorJ\SympaSession\DataTypes;

use GregorJ\SympaSession\Exceptions\InvalidSympaRobotException;

/**
 *
 */
final class SympaRobot
{
    private string $host;

    private string $path;

    /**
     * @param string $robot
     * @throws InvalidSympaRobotException
     */
    public function __construct(string $robot)
    {
        if (filter_var($robot, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED) === false) {
            throw new InvalidSympaRobotException(sprintf('Invalid sympa robot URL \'%s\'!', $robot));
        }
        $this->host = parse_url($robot, PHP_URL_HOST);
        $this->path = parse_url($robot, PHP_URL_PATH);
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return sprintf('%s%s', $this->host, $this->path);
    }
}

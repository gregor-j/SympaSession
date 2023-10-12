<?php

declare(strict_types=1);

namespace GregorJ\SympaSession\DataTypes;

use GregorJ\SympaSession\Exceptions\UnknownSessionDataKeyException;

/**
 *
 */
final class SessionData
{
    public const KEY_AUTH = 'auth';

    public const KEY_DATA = 'data';

    public const AUTH_CLASSIC = 'classic';

    private array $data = [];

    /**
     * @param string $string
     */
    public function __construct(string $string = '')
    {
        if ($string === '') {
            $string = sprintf(';%s="%s";%s=""', self::KEY_AUTH, self::AUTH_CLASSIC, self::KEY_DATA);
        }
        $this->setFromString($string);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function exists(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * @param string $key
     * @return mixed
     * @throws UnknownSessionDataKeyException
     */
    public function get(string $key)
    {
        if ($this->exists($key)) {
            return $this->data[$key];
        }
        throw new UnknownSessionDataKeyException(sprintf('Invalid session data key \'%s\'!', $key));
    }

    /**
     * @param string $key
     * @param $value
     * @return void
     */
    public function set(string $key, $value): void
    {
        $this->data[$key] = $value;
    }

    /**
     * @param string $string
     * @return void
     */
    public function setFromString(string $string): void
    {
        while (preg_match('~^(\;?(\w+)\="([^"]*)")~', $string, $matches)) {
            $this->data[$matches[2]] = $matches[3];
            $string = preg_replace("~$matches[1]~", '', $string) ;
        }
    }

    /**
     * @return bool
     */
    public function isAuthClassic(): bool
    {
        if ($this->exists(self::KEY_AUTH)) {
            return $this->data[self::KEY_AUTH] === self::AUTH_CLASSIC;
        }
        return false;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $string = '';
        foreach ($this->data as $key => $value) {
            $string .= ';' . $key . '="' . $value . '"';
        }
        return $string;
    }
}

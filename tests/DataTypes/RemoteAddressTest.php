<?php

declare(strict_types=1);

namespace Tests\GregorJ\SympaSession\DataTypes;

use GregorJ\SympaSession\DataTypes\RemoteAddress;
use GregorJ\SympaSession\Exceptions\InvalidRemoteAddressException;
use PHPUnit\Framework\TestCase;

/**
 * Test DataTypes\RemoteAddress class.
 */
class RemoteAddressTest extends TestCase
{
    /**
     * @return void
     */
    public function testConstructorAndToString()
    {
        $host = new RemoteAddress('10.1.2.3');
        static::assertSame('10.1.2.3', (string)$host);
    }

    /**
     * @return void
     */
    public function testEquals()
    {
        $host1 = new RemoteAddress('10.0.0.1');
        $host2 = new RemoteAddress('10.0.0.2');
        $host3 = new RemoteAddress('10.0.0.1');
        static::assertFalse($host1->equals($host2));
        static::assertFalse($host2->equals($host1));
        static::assertFalse($host3->equals($host2));
        static::assertFalse($host2->equals($host3));
        static::assertTrue($host1->equals($host3));
        static::assertTrue($host3->equals($host1));
    }

    /**
     * Provide invalid email addresses
     * @return array[]
     */
    public static function provideInvalidHosts(): array
    {
        return [
            ['akjlksjdf'],
            ['jkljklsdjflksdf.com'],

        ];
    }

    /**
     * @param string $host
     * @return void
     * @dataProvider provideInvalidHosts
     */
    public function testInvalidHost(string $host): void
    {
        self::expectException(InvalidRemoteAddressException::class);
        self::expectExceptionMessage(sprintf('Invalid remote address \'%s\'!', $host));
        new RemoteAddress($host);
    }
}

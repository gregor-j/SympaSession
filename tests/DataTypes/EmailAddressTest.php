<?php

declare(strict_types=1);

namespace Tests\GregorJ\SympaSession\DataTypes;

use GregorJ\SympaSession\DataTypes\EmailAddress;
use GregorJ\SympaSession\Exceptions\InvalidEmailException;
use PHPUnit\Framework\TestCase;

/**
 * Test DataTypes\EmailAddress class.
 */
class EmailAddressTest extends TestCase
{
    /**
     * @return void
     */
    public function testConstructorAndToString(): void
    {
        $email = new EmailAddress('Example@Example.com');
        static::assertSame('example@example.com', (string)$email);
    }

    /**
     * @return void
     */
    public function testEquals(): void
    {
        $email1 = new EmailAddress('example1@example.com');
        $email2 = new EmailAddress('example2@example.com');
        $email3 = new EmailAddress('example1@example.com');
        static::assertFalse($email1->equals($email2));
        static::assertFalse($email2->equals($email1));
        static::assertFalse($email3->equals($email2));
        static::assertFalse($email2->equals($email3));
        static::assertTrue($email1->equals($email3));
        static::assertTrue($email3->equals($email1));
    }

    /**
     * Provide invalid email addresses
     * @return array[]
     */
    public static function provideInvalidAddresses(): array
    {
        return [
            ['akjlksjdf'],
            ['jkljklsdjflksdf.com'],

        ];
    }

    /**
     * @param string $email
     * @return void
     * @dataProvider provideInvalidAddresses
     */
    public function testInvalidAddress(string $email): void
    {
        self::expectException(InvalidEmailException::class);
        self::expectExceptionMessage(sprintf('Invalid email address \'%s\'!', $email));
        new EmailAddress($email);
    }
}

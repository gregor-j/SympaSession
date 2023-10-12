<?php

namespace Tests\GregorJ\SympaSession\DataTypes;

use GregorJ\SympaSession\DataTypes\SessionId;
use PHPUnit\Framework\TestCase;

/**
 * DataTypes\SessionId
 */
class SessionIdTest extends TestCase
{
    /**
     * @return void
     */
    public function testConstructAndToString()
    {
        $id = new SessionId('10000000000000');
        static::assertSame('10000000000000', (string)$id);
        static::assertSame('10000000000000', $id->getId());
    }
}

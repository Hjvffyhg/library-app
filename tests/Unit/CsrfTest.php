<?php

declare(strict_types=1);

use App\Core\Csrf;
use PHPUnit\Framework\TestCase;

final class CsrfTest extends TestCase
{
    protected function setUp(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $_SESSION = [];
    }

    public function testTokenIsCreatedAndAccepted(): void
    {
        $token = Csrf::token();

        self::assertSame(64, strlen($token));
        self::assertTrue(Csrf::isValid($token));
    }

    public function testDifferentTokenIsRejected(): void
    {
        $token = Csrf::token();

        self::assertFalse(Csrf::isValid($token . 'changed'));
        self::assertFalse(Csrf::isValid(null));
    }

    public function testTokenIsReplacedWhenRegenerated(): void
    {
        $first = Csrf::token();
        Csrf::regenerate();
        $second = Csrf::token();

        self::assertNotSame($first, $second);
        self::assertFalse(Csrf::isValid($first));
        self::assertTrue(Csrf::isValid($second));
    }
}

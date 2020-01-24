<?php declare(strict_types=1);

namespace Arus\Monolog\Handler\Tests;

/**
 * Import classes
 */
use Arus\Monolog\Handler\TelegramHandler;
use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * Import functions
 */
use function putenv;

/**
 * TelegramHandlerTest
 */
class TelegramHandlerTest extends TestCase
{

    /**
     * @return void
     */
    public function testContracts() : void
    {
        $handler = new TelegramHandler(
            $_ENV['TELEGRAM_TOKEN'],
            $_ENV['TELEGRAM_RECIPIENT']
        );

        $this->assertInstanceOf(AbstractProcessingHandler::class, $handler);
    }

    /**
     * @return void
     */
    public function testConstructorWithRequiredArguments() : void
    {
        $handler = new TelegramHandler(
            $_ENV['TELEGRAM_TOKEN'],
            $_ENV['TELEGRAM_RECIPIENT']
        );

        $this->assertEquals(
            $_ENV['TELEGRAM_TOKEN'],
            $handler->getToken()
        );

        $this->assertEquals(
            $_ENV['TELEGRAM_RECIPIENT'],
            $handler->getRecipient()
        );

        $this->assertEquals(
            Logger::DEBUG,
            $handler->getLevel()
        );

        $this->assertEquals(
            true,
            $handler->getBubble()
        );
    }

    /**
     * @return void
     */
    public function testConstructorWithOptionalArguments() : void
    {
        $handler = new TelegramHandler(
            $_ENV['TELEGRAM_TOKEN'],
            $_ENV['TELEGRAM_RECIPIENT'],
            Logger::ERROR,
            false
        );

        $this->assertEquals(
            $_ENV['TELEGRAM_TOKEN'],
            $handler->getToken()
        );

        $this->assertEquals(
            $_ENV['TELEGRAM_RECIPIENT'],
            $handler->getRecipient()
        );

        $this->assertEquals(
            Logger::ERROR,
            $handler->getLevel()
        );

        $this->assertEquals(
            false,
            $handler->getBubble()
        );
    }

    /**
     * @return void
     */
    public function testDefaultUrl() : void
    {
        $handler = new TelegramHandler(
            $_ENV['TELEGRAM_TOKEN'],
            $_ENV['TELEGRAM_RECIPIENT']
        );

        $this->assertSame('https://api.telegram.org', $handler->getUrl());
    }

    /**
     * @return void
     */
    public function testSetUrlThroughMethod() : void
    {
        $handler = new TelegramHandler(
            $_ENV['TELEGRAM_TOKEN'],
            $_ENV['TELEGRAM_RECIPIENT']
        );

        $handler->setUrl('http://localhost');

        $this->assertSame('http://localhost', $handler->getUrl());
    }

    /**
     * @runInSeparateProcess
     *
     * @return void
     */
    public function testSetUrlThroughEnvironment() : void
    {
        putenv('TELEGRAM_URL=http://127.0.0.1');

        $handler = new TelegramHandler(
            $_ENV['TELEGRAM_TOKEN'],
            $_ENV['TELEGRAM_RECIPIENT']
        );

        $this->assertSame('http://127.0.0.1', $handler->getUrl());
    }

    /**
     * @return void
     */
    public function testSendAnimation() : void
    {
        $logger = new Logger(__FUNCTION__);

        $logger->pushHandler(new TelegramHandler(
            $_ENV['TELEGRAM_TOKEN'],
            $_ENV['TELEGRAM_RECIPIENT']
        ));

        $logger->debug(__FUNCTION__, [
            'animation' => 'https://www.easygifanimator.net/images/samples/texteffects.gif',
        ]);

        $this->assertTrue(true);
    }

    /**
     * @return void
     */
    public function testSendPhoto() : void
    {
        $logger = new Logger(__FUNCTION__);

        $logger->pushHandler(new TelegramHandler(
            $_ENV['TELEGRAM_TOKEN'],
            $_ENV['TELEGRAM_RECIPIENT']
        ));

        $logger->debug(__FUNCTION__, [
            'photo' => 'https://telegram.org/img/t_logo.png',
        ]);

        $this->assertTrue(true);
    }

    /**
     * @return void
     */
    public function testSendVideo() : void
    {
        $logger = new Logger(__FUNCTION__);

        $logger->pushHandler(new TelegramHandler(
            $_ENV['TELEGRAM_TOKEN'],
            $_ENV['TELEGRAM_RECIPIENT']
        ));

        $logger->debug(__FUNCTION__, [
            'video' => 'http://techslides.com/demos/sample-videos/small.mp4',
        ]);

        $this->assertTrue(true);
    }

    /**
     * @return void
     */
    public function testSendMessage() : void
    {
        $logger = new Logger(__FUNCTION__);

        $logger->pushHandler(new TelegramHandler(
            $_ENV['TELEGRAM_TOKEN'],
            $_ENV['TELEGRAM_RECIPIENT']
        ));

        $logger->debug(__FUNCTION__);

        $this->assertTrue(true);
    }

    /**
     * @return void
     */
    public function testInvalidToken() : void
    {
        $logger = new Logger(__FUNCTION__);

        $logger->pushHandler(new TelegramHandler(
            '000000000:000000000ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            $_ENV['TELEGRAM_RECIPIENT']
        ));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Telegram error: Unauthorized');

        $logger->debug(__FUNCTION__);
    }

    /**
     * @return void
     */
    public function testInvalidRecipient() : void
    {
        $logger = new Logger(__FUNCTION__);

        $logger->pushHandler(new TelegramHandler(
            $_ENV['TELEGRAM_TOKEN'],
            '000000000'
        ));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Telegram error: Bad Request: chat not found');

        $logger->debug(__FUNCTION__);
    }
}

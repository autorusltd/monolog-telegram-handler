<?php declare(strict_types=1);

namespace Arus\Monolog\Handler\Tests;

/**
 * Import classes
 */
use Arus\Monolog\Handler\TelegramHandler;
use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Handler\HandlerInterface;
use PHPUnit\Framework\TestCase;

/**
 * TelegramHandlerTest
 */
class TelegramHandlerTest extends TestCase
{
    // phpcs:disable
    private const EXAMPLE_PHOTO = 'https://pixabay.com/get/52e1d7404956ac14f1dc8460825668204022dfe05550724f7d2a72d4/eyes-4123340_640.jpg';
    private const EXAMPLE_ANIMATION = 'https://www.easygifanimator.net/images/samples/texteffects.gif';
    private const EXAMPLE_VIDEO = 'http://techslides.com/demos/sample-videos/small.mp4';
    // phpcs:enable

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
        $this->assertInstanceOf(HandlerInterface::class, $handler);
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
            Logger::WARNING,
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
            Logger::WARNING,
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
    public function testSendMessageThroughRecordContext() : void
    {
        $logger = new Logger(__METHOD__);

        $logger->pushHandler(new TelegramHandler(
            $_ENV['TELEGRAM_TOKEN'],
            $_ENV['TELEGRAM_RECIPIENT']
        ));

        $logger->debug(__METHOD__);

        $this->assertTrue(true);
    }

    /**
     * @return void
     */
    public function testSendAnimationThroughRecordContext() : void
    {
        $logger = new Logger(__METHOD__);

        $logger->pushHandler(new TelegramHandler(
            $_ENV['TELEGRAM_TOKEN'],
            $_ENV['TELEGRAM_RECIPIENT']
        ));

        $logger->debug(__METHOD__, [
            'animation' => self::EXAMPLE_ANIMATION,
        ]);

        $this->assertTrue(true);
    }

    /**
     * @return void
     */
    public function testSendPhotoThroughRecordContext() : void
    {
        $logger = new Logger(__METHOD__);

        $logger->pushHandler(new TelegramHandler(
            $_ENV['TELEGRAM_TOKEN'],
            $_ENV['TELEGRAM_RECIPIENT']
        ));

        $logger->debug(__METHOD__, [
            'photo' => self::EXAMPLE_PHOTO,
        ]);

        $this->assertTrue(true);
    }

    /**
     * @return void
     */
    public function testSendVideoThroughRecordContext() : void
    {
        $logger = new Logger(__METHOD__);

        $logger->pushHandler(new TelegramHandler(
            $_ENV['TELEGRAM_TOKEN'],
            $_ENV['TELEGRAM_RECIPIENT']
        ));

        $logger->debug(__METHOD__, [
            'video' => self::EXAMPLE_VIDEO,
        ]);

        $this->assertTrue(true);
    }

    /**
     * @return void
     */
    public function testUrl() : void
    {
        $handler = new TelegramHandler(
            $_ENV['TELEGRAM_TOKEN'],
            $_ENV['TELEGRAM_RECIPIENT']
        );

        $this->assertSame('https://api.telegram.org', $handler->getUrl());

        $handler->setUrl('http://localhost');

        $this->assertSame('http://localhost', $handler->getUrl());
    }
}

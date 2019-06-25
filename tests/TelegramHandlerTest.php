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
use Psr\Log\LoggerInterface;

/**
 * Import functions
 */
use function json_decode;

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
            [$_ENV['TELEGRAM_RECIPIENT']]
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
            [$_ENV['TELEGRAM_RECIPIENT']]
        );

        $this->assertEquals(
            $_ENV['TELEGRAM_TOKEN'],
            $handler->getToken()
        );

        $this->assertEquals(
            [$_ENV['TELEGRAM_RECIPIENT']],
            $handler->getRecipients()
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
            [$_ENV['TELEGRAM_RECIPIENT']],
            Logger::WARNING,
            false
        );

        $this->assertEquals(
            $_ENV['TELEGRAM_TOKEN'],
            $handler->getToken()
        );

        $this->assertEquals(
            [$_ENV['TELEGRAM_RECIPIENT']],
            $handler->getRecipients()
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
    public function testSendMessage() : void
    {
        $handler = new TelegramHandler(
            $_ENV['TELEGRAM_TOKEN'],
            [$_ENV['TELEGRAM_RECIPIENT']]
        );

        $data = json_decode($handler->sendMessage([
            'formatted' => __METHOD__,
        ], false), true);

        $this->assertTrue($data['ok']);
    }

    /**
     * @return void
     */
    public function testSendAnimation() : void
    {
        $handler = new TelegramHandler(
            $_ENV['TELEGRAM_TOKEN'],
            [$_ENV['TELEGRAM_RECIPIENT']]
        );

        $data = json_decode($handler->sendAnimation([
            'formatted' => __METHOD__,
            'context' => [
                'animation' => self::EXAMPLE_ANIMATION,
            ],
        ], false), true);

        $this->assertTrue($data['ok']);
    }

    /**
     * @return void
     */
    public function testSendPhoto() : void
    {
        $handler = new TelegramHandler(
            $_ENV['TELEGRAM_TOKEN'],
            [$_ENV['TELEGRAM_RECIPIENT']]
        );

        $data = json_decode($handler->sendPhoto([
            'formatted' => __METHOD__,
            'context' => [
                'photo' => self::EXAMPLE_PHOTO,
            ],
        ], false), true);

        $this->assertTrue($data['ok']);
    }

    /**
     * @return void
     */
    public function testSendVideo() : void
    {
        $handler = new TelegramHandler(
            $_ENV['TELEGRAM_TOKEN'],
            [$_ENV['TELEGRAM_RECIPIENT']]
        );

        $data = json_decode($handler->sendVideo([
            'formatted' => __METHOD__,
            'context' => [
                'video' => self::EXAMPLE_VIDEO,
            ],
        ], false), true);

        $this->assertTrue($data['ok']);
    }

    /**
     * @return void
     */
    public function testSilentSendMessage() : void
    {
        $handler = new TelegramHandler(
            $_ENV['TELEGRAM_TOKEN'],
            [$_ENV['TELEGRAM_RECIPIENT']]
        );

        $this->assertNull($handler->sendMessage([
            'formatted' => __METHOD__,
        ], true));
    }

    /**
     * @return void
     */
    public function testSilentSendAnimation() : void
    {
        $handler = new TelegramHandler(
            $_ENV['TELEGRAM_TOKEN'],
            [$_ENV['TELEGRAM_RECIPIENT']]
        );

        $this->assertNull($handler->sendAnimation([
            'formatted' => __METHOD__,
            'context' => [
                'animation' => self::EXAMPLE_ANIMATION,
            ],
        ], true));
    }

    /**
     * @return void
     */
    public function testSilentSendPhoto() : void
    {
        $handler = new TelegramHandler(
            $_ENV['TELEGRAM_TOKEN'],
            [$_ENV['TELEGRAM_RECIPIENT']]
        );

        $this->assertNull($handler->sendPhoto([
            'formatted' => __METHOD__,
            'context' => [
                'photo' => self::EXAMPLE_PHOTO,
            ],
        ], true));
    }

    /**
     * @return void
     */
    public function testSilentSendVideo() : void
    {
        $handler = new TelegramHandler(
            $_ENV['TELEGRAM_TOKEN'],
            [$_ENV['TELEGRAM_RECIPIENT']]
        );

        $this->assertNull($handler->sendVideo([
            'formatted' => __METHOD__,
            'context' => [
                'video' => self::EXAMPLE_VIDEO,
            ],
        ], true));
    }

    /**
     * @return void
     */
    public function testSendMessageThroughRecordContext() : void
    {
        $handler = new TelegramHandler(
            $_ENV['TELEGRAM_TOKEN'],
            [$_ENV['TELEGRAM_RECIPIENT']]
        );

        $data = json_decode($handler->send([
            'formatted' => __METHOD__,
        ], false), true);

        $this->assertTrue($data['ok']);
    }

    /**
     * @return void
     */
    public function testSendAnimationThroughRecordContext() : void
    {
        $handler = new TelegramHandler(
            $_ENV['TELEGRAM_TOKEN'],
            [$_ENV['TELEGRAM_RECIPIENT']]
        );

        $data = json_decode($handler->send([
            'formatted' => __METHOD__,
            'context' => [
                'animation' => self::EXAMPLE_ANIMATION,
            ],
        ], false), true);

        $this->assertTrue($data['ok']);
        $this->assertArrayHasKey('animation', $data['result']);
    }

    /**
     * @return void
     */
    public function testSendPhotoThroughRecordContext() : void
    {
        $handler = new TelegramHandler(
            $_ENV['TELEGRAM_TOKEN'],
            [$_ENV['TELEGRAM_RECIPIENT']]
        );

        $data = json_decode($handler->send([
            'formatted' => __METHOD__,
            'context' => [
                'photo' => self::EXAMPLE_PHOTO,
            ],
        ], false), true);

        $this->assertTrue($data['ok']);
        $this->assertArrayHasKey('photo', $data['result']);
    }

    /**
     * @return void
     */
    public function testSendVideoThroughRecordContext() : void
    {
        $handler = new TelegramHandler(
            $_ENV['TELEGRAM_TOKEN'],
            [$_ENV['TELEGRAM_RECIPIENT']]
        );

        $data = json_decode($handler->send([
            'formatted' => __METHOD__,
            'context' => [
                'video' => self::EXAMPLE_VIDEO,
            ],
        ], false), true);

        $this->assertTrue($data['ok']);
        $this->assertArrayHasKey('video', $data['result']);
    }

    /**
     * @return void
     */
    public function testSilentSendMessageThroughRecordContext() : void
    {
        $handler = new TelegramHandler(
            $_ENV['TELEGRAM_TOKEN'],
            [$_ENV['TELEGRAM_RECIPIENT']]
        );

        $this->assertNull($handler->send([
            'formatted' => __METHOD__,
        ], true));
    }

    /**
     * @return void
     */
    public function testSilentSendAnimationThroughRecordContext() : void
    {
        $handler = new TelegramHandler(
            $_ENV['TELEGRAM_TOKEN'],
            [$_ENV['TELEGRAM_RECIPIENT']]
        );

        $this->assertNull($handler->send([
            'formatted' => __METHOD__,
            'context' => [
                'animation' => self::EXAMPLE_ANIMATION,
            ],
        ], true));
    }

    /**
     * @return void
     */
    public function testSilentSendPhotoThroughRecordContext() : void
    {
        $handler = new TelegramHandler(
            $_ENV['TELEGRAM_TOKEN'],
            [$_ENV['TELEGRAM_RECIPIENT']]
        );

        $this->assertNull($handler->send([
            'formatted' => __METHOD__,
            'context' => [
                'photo' => self::EXAMPLE_PHOTO,
            ],
        ], true));
    }

    /**
     * @return void
     */
    public function testSilentSendVideoThroughRecordContext() : void
    {
        $handler = new TelegramHandler(
            $_ENV['TELEGRAM_TOKEN'],
            [$_ENV['TELEGRAM_RECIPIENT']]
        );

        $this->assertNull($handler->send([
            'formatted' => __METHOD__,
            'context' => [
                'video' => self::EXAMPLE_VIDEO,
            ],
        ], true));
    }

    /**
     * @return void
     */
    public function testMonolog() : void
    {
        $handler = new TelegramHandler(
            $_ENV['TELEGRAM_TOKEN'],
            [$_ENV['TELEGRAM_RECIPIENT']]
        );

        $logger = new Logger(__METHOD__);
        $logger->pushHandler($handler);

        $this->assertTrue($logger->debug(__METHOD__));
    }
}

<?php declare(strict_types=1);

namespace Arus\Monolog\Handler;

/**
 * Import classes
 */
use Monolog\Logger;
use Monolog\Handler\Curl;
use Monolog\Handler\AbstractProcessingHandler;
use RuntimeException;

/**
 * Import functions
 */
use function curl_init;
use function curl_setopt_array;
use function getenv;
use function http_build_query;
use function json_decode;
use function sprintf;

/**
 * Import constants
 */
use const CURLOPT_POSTFIELDS;
use const CURLOPT_RETURNTRANSFER;
use const CURLOPT_URL;

/**
 * TelegramHandler
 */
class TelegramHandler extends AbstractProcessingHandler
{

    /**
     * Telegram API URL
     *
     * @var string
     */
    private $url;

    /**
     * Telegram API token
     *
     * @var string
     */
    private $token;

    /**
     * Messages recipient
     *
     * @var string
     */
    private $recipient;

    /**
     * Constructor of the class
     *
     * @param string $token
     * @param string $recipient
     * @param mixed $minLevel
     * @param bool $bubble
     */
    public function __construct(string $token, string $recipient, $minLevel = Logger::DEBUG, bool $bubble = true)
    {
        $this->url = getenv('TELEGRAM_URL') ?: 'https://api.telegram.org';
        $this->token = $token;
        $this->recipient = $recipient;

        parent::__construct($minLevel, $bubble);
    }

    /**
     * Sets the given telegram API URL to the handler
     *
     * @param string $url
     *
     * @return void
     */
    public function setUrl(string $url) : void
    {
        $this->url = $url;
    }

    /**
     * Gets telegram API URL
     *
     * @return string
     */
    public function getUrl() : string
    {
        return $this->url;
    }

    /**
     * Gets telegram API token
     *
     * @return string
     */
    public function getToken() : string
    {
        return $this->token;
    }

    /**
     * Gets messages recipient
     *
     * @return string
     */
    public function getRecipient() : string
    {
        return $this->recipient;
    }

    /**
     * {@inheritDoc}
     */
    protected function write(array $record) : void
    {
        if (isset($record['context']['animation'])) {
            $this->send('sendAnimation', [
                'chat_id' => $this->recipient,
                'animation' => $record['context']['animation'],
                'caption' => $record['formatted'],
            ]);
        } elseif (isset($record['context']['photo'])) {
            $this->send('sendPhoto', [
                'chat_id' => $this->recipient,
                'photo' => $record['context']['photo'],
                'caption' => $record['formatted'],
            ]);
        } elseif (isset($record['context']['video'])) {
            $this->send('sendVideo', [
                'chat_id' => $this->recipient,
                'video' => $record['context']['video'],
                'caption' => $record['formatted'],
            ]);
        } else {
            $this->send('sendMessage', [
                'chat_id' => $this->recipient,
                'text' => $record['formatted'],
            ]);
        }
    }

    /**
     * Sends a message with the given data through the given telegram API method
     *
     * @param string $method
     * @param array $data
     *
     * @return void
     *
     * @throws RuntimeException
     *
     * @link https://core.telegram.org/bots/api#sendanimation
     * @link https://core.telegram.org/bots/api#sendphoto
     * @link https://core.telegram.org/bots/api#sendvideo
     * @link https://core.telegram.org/bots/api#sendmessage
     */
    protected function send(string $method, array $data) : void
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => sprintf('%s/bot%s/%s', $this->url, $this->token, $method),
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_RETURNTRANSFER => true,
        ]);

        $result = Curl\Util::execute($ch);
        $response = json_decode($result, true);

        if (false === $response['ok']) {
            throw new RuntimeException(
                sprintf('Telegram error: %s', $response['description'])
            );
        }
    }
}

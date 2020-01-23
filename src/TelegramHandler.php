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
use function getenv;
use function escapeshellarg;
use function json_encode;

/**
 * TelegramHandler
 */
class TelegramHandler extends AbstractProcessingHandler
{

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $token;

    /**
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
     * @param string $url
     *
     * @return void
     */
    public function setUrl(string $url) : void
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getUrl() : string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getToken() : string
    {
        return $this->token;
    }

    /**
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
                'animation' => $record['context']['animation'],
                'caption' => $record['formatted'],
            ]);
        } elseif (isset($record['context']['photo'])) {
            $this->send('sendPhoto', [
                'photo' => $record['context']['photo'],
                'caption' => $record['formatted'],
            ]);
        } elseif (isset($record['context']['video'])) {
            $this->send('sendVideo', [
                'video' => $record['context']['video'],
                'caption' => $record['formatted'],
            ]);
        } else {
            $this->send('sendMessage', [
                'text' => $record['formatted'],
            ]);
        }
    }

    /**
     * @param string $method
     * @param array $data
     *
     * @return void
     *
     * @link https://core.telegram.org/bots/api#sendanimation
     * @link https://core.telegram.org/bots/api#sendphoto
     * @link https://core.telegram.org/bots/api#sendvideo
     * @link https://core.telegram.org/bots/api#sendmessage
     */
    protected function send(string $method, array $data) : void
    {
        $data['chat_id'] = $this->recipient;

        $url = sprintf('%s/bot%s/%s', $this->url, $this->token, $method);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

        $result = Curl\Util::execute($ch);
        $result = json_decode($result, true);

        if ($result['ok'] === false) {
            throw new RuntimeException('Telegram API error. Description: ' . $result['description']);
        }
    }
}

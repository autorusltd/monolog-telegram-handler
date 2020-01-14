<?php declare(strict_types=1);

namespace Arus\Monolog\Handler;

/**
 * Import classes
 */
use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;

/**
 * Import functions
 */
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
    private $url = 'https://api.telegram.org';

    /**
     * @var string
     */
    private $token;

    /**
     * @var array
     */
    private $recipients;

    /**
     * @var int
     */
    private $jsonOptions = 0;

    /**
     * @var int
     */
    private $jsonDepth = 512;

    /**
     * @param string $token
     * @param array $recipients
     * @param int $level
     * @param bool $bubble
     */
    public function __construct(
        string $token,
        array $recipients,
        int $level = Logger::DEBUG,
        bool $bubble = true
    ) {
        $this->token = $token;
        $this->recipients = $recipients;

        parent::__construct($level, $bubble);
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
     * @param int $jsonOptions
     *
     * @return void
     */
    public function setJsonOptions(int $jsonOptions) : void
    {
        $this->jsonOptions = $jsonOptions;
    }

    /**
     * @param int $jsonDepth
     *
     * @return void
     */
    public function setJsonDepth(int $jsonDepth) : void
    {
        $this->jsonDepth = $jsonDepth;
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
     * @return array
     */
    public function getRecipients() : array
    {
        return $this->recipients;
    }

    /**
     * @return int
     */
    public function getJsonOptions() : int
    {
        return $this->jsonOptions;
    }

    /**
     * @return int
     */
    public function getJsonDepth() : int
    {
        return $this->jsonDepth;
    }

    /**
     * @param array $record
     * @param bool $silent
     *
     * @return null|string
     */
    public function send(array $record, bool $silent) : ?string
    {
        if (isset($record['context']['animation'])) {
            return $this->sendAnimation($record, $silent);
        } elseif (isset($record['context']['photo'])) {
            return $this->sendPhoto($record, $silent);
        } elseif (isset($record['context']['video'])) {
            return $this->sendVideo($record, $silent);
        }

        return $this->sendMessage($record, $silent);
    }

    /**
     * @param array $record
     * @param bool $silent
     *
     * @return null|string
     *
     * @link https://core.telegram.org/bots/api#sendmessage
     */
    public function sendMessage(array $record, bool $silent) : ?string
    {
        return $this->process(__FUNCTION__, [
            'text' => $record['formatted'],
        ], $silent);
    }

    /**
     * @param array $record
     * @param bool $silent
     *
     * @return null|string
     *
     * @link https://core.telegram.org/bots/api#sendanimation
     */
    public function sendAnimation(array $record, bool $silent) : ?string
    {
        return $this->process(__FUNCTION__, [
            'animation' => $record['context']['animation'],
            'caption' => $record['formatted'],
        ], $silent);
    }

    /**
     * @param array $record
     * @param bool $silent
     *
     * @return null|string
     *
     * @link https://core.telegram.org/bots/api#sendphoto
     */
    public function sendPhoto(array $record, bool $silent) : ?string
    {
        return $this->process(__FUNCTION__, [
            'photo' => $record['context']['photo'],
            'caption' => $record['formatted'],
        ], $silent);
    }

    /**
     * @param array $record
     * @param bool $silent
     *
     * @return null|string
     *
     * @link https://core.telegram.org/bots/api#sendvideo
     */
    public function sendVideo(array $record, bool $silent) : ?string
    {
        return $this->process(__FUNCTION__, [
            'video' => $record['context']['video'],
            'caption' => $record['formatted'],
        ], $silent);
    }

    /**
     * {@inheritDoc}
     */
    protected function write(array $record) : void
    {
        $this->send($record, true);
    }

    /**
     * @param string $method
     * @param array $params
     * @param bool $silent
     *
     * @return null|string
     */
    protected function process(string $method, array $params, bool $silent) : ?string
    {
        $uri = escapeshellarg("{$this->url}/bot{$this->token}/{$method}");

        $output = '';

        foreach ($this->recipients as $recipient) {
            $json = json_encode(
                $params + ['chat_id' => $recipient],
                $this->jsonOptions,
                $this->jsonDepth
            );

            $data = escapeshellarg($json);

            if ($silent) {
                `curl -s -X 'POST' -H 'Content-Type: application/json' -d $data $uri > /dev/null 2>&1 &`;
            } else {
                $output .= `curl -s -X 'POST' -H 'Content-Type: application/json' -d $data $uri`;
            }
        }

        return ! $silent ? $output : null;
    }
}

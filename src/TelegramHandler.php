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
    private $token;

    /**
     * @var array
     */
    private $recipients;

    /**
     * @param string $token
     * @param array $recipients
     * @param int $level
     * @param bool $bubble
     */
    public function __construct(string $token, array $recipients, int $level = Logger::DEBUG, bool $bubble = true)
    {
        $this->token = $token;
        $this->recipients = $recipients;

        parent::__construct($level, $bubble);
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
        $uri = escapeshellarg("https://api.telegram.org/bot{$this->token}/{$method}");

        $output = '';

        foreach ($this->recipients as $recipient) {
            $data = escapeshellarg(json_encode(($params + ['chat_id' => $recipient]), JSON_UNESCAPED_UNICODE));

            if ($silent) {
                `curl -s -X 'POST' -H 'Content-Type: application/json' -d $data $uri > /dev/null 2>&1 &`;
            } else {
                $output .= `curl -s -X 'POST' -H 'Content-Type: application/json' -d $data $uri`;
            }
        }

        return ! $silent ? $output : null;
    }
}

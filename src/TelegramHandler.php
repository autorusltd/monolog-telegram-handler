<?php declare(strict_types=1);

namespace Arus\Monolog\Handler;

/**
 * Import classes
 */
use Monolog\Logger;
use Monolog\Handler\AbstractHandler;

/**
 * Import functions
 */
use function escapeshellarg;
use function json_encode;

/**
 * TelegramHandler
 */
class TelegramHandler extends AbstractHandler
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
     * {@inheritDoc}
     */
    public function handle(array $record) : bool
    {
        $this->send($record, true);

        return true;
    }

    /**
     * @param array $record
     * @param bool $silent
     *
     * @return null|string
     */
    public function send(array $record, bool $silent = true) : ?string
    {
        if (isset($record['context']['photo'])) {
            return $this->sendPhoto($record, $silent);
        }

        if (isset($record['context']['animation'])) {
            return $this->sendAnimation($record, $silent);
        }

        if (isset($record['context']['video'])) {
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
    public function sendMessage(array $record, bool $silent = true) : ?string
    {
        return $this->process(__FUNCTION__, [
            'text' => $record['message'],
            'parse_mode' => 'Markdown',
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
    public function sendPhoto(array $record, bool $silent = true) : ?string
    {
        return $this->process(__FUNCTION__, [
            'photo' => $record['context']['photo'],
            'caption' => $record['message'],
            'parse_mode' => 'Markdown',
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
    public function sendAnimation(array $record, bool $silent = true) : ?string
    {
        return $this->process(__FUNCTION__, [
            'animation' => $record['context']['animation'],
            'caption' => $record['message'],
            'parse_mode' => 'Markdown',
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
    public function sendVideo(array $record, bool $silent = true) : ?string
    {
        return $this->process(__FUNCTION__, [
            'video' => $record['context']['video'],
            'caption' => $record['message'],
            'parse_mode' => 'Markdown',
        ], $silent);
    }

    /**
     * @param string $method
     * @param array $params
     * @param bool $silent
     *
     * @return null|string
     */
    private function process(string $method, array $params, bool $silent = true) : ?string
    {
        $uri = escapeshellarg("https://api.telegram.org/bot{$this->token}/{$method}");

        $output = '';

        foreach ($this->recipients as $recipient) {
            $data = escapeshellarg(json_encode($params + ['chat_id' => $recipient]));

            if ($silent) {
                `curl -s -X 'POST' -H 'Content-Type: application/json' -d $data $uri > /dev/null 2>&1 &`;
            } else {
                $output .= `curl -s -X 'POST' -H 'Content-Type: application/json' -d $data $uri`;
            }
        }

        return ! $silent ? $output : null;
    }
}

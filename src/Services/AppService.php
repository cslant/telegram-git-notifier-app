<?php

namespace TelegramGithubNotify\App\Services;

use Exception;
use Telegram;

class AppService
{
    public Telegram $telegram;

    public string $chatId;

    public function __construct()
    {
        $this->telegram = new Telegram(config('telegram-bot.token'));
        $this->chatId = config('telegram-bot.chat_id');
    }

    /**
     * Send a message to telegram
     *
     * @param string $message
     * @param array $options
     * @param string $sendType
     * @return void
     */
    public function sendMessage(string $message = '', array $options = [], string $sendType = 'Message'): void
    {
        $content = array(
            'chat_id' => $this->chatId,
            'disable_web_page_preview' => true,
            'parse_mode' => 'HTML'
        );

        try {
            if ($sendType === 'Message') {
                $content['text'] = $message;
            } elseif ($sendType === 'Photo' && !empty($options)) {
                $content['photo'] = $options['photo'];
                $content['caption'] = $message;
            }

            $this->telegram->{'send' . $sendType}(array_merge($content, $options));
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
    }

    /**
     * Send callback response to telegram (show alert)
     *
     * @param string|null $text
     * @return void
     */
    public function answerCallbackQuery(string $text = null): void
    {
        try {
            $this->telegram->answerCallbackQuery([
                'callback_query_id' => $this->telegram->Callback_ID(),
                'text' => $text,
                'show_alert' => true
            ]);
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
    }
}

<?php

namespace TelegramGithubNotify\App\Services;

use Exception;
use Telegram;

class AppService
{
    public Telegram $telegram;

    public function __construct()
    {
        $this->telegram = new Telegram(config('telegram-bot.token'));
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
            'chat_id' => config('telegram-bot.chat_id'),
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

            if (!empty($options) && isset($options['reply_markup'])) {
                $content['reply_markup'] = $this->telegram->buildInlineKeyBoard($options['reply_markup']);
            }

            $this->telegram->{'send' . $sendType}($content);
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
    }
}

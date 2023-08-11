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

        if ($sendType === 'Message') {
            $content['text'] = $message;
        } elseif ($sendType === 'Photo') {
            $content['photo'] = $options['photo'] ?? null;
            $content['caption'] = $message;
        }

        $content['reply_markup'] = $options['reply_markup'] ? $this->telegram->buildInlineKeyBoard($options['reply_markup']) : null;

        $this->telegram->{'send' . $sendType}($content);
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

    /**
     * Edit message from telegram
     * (Edit message text and reply markup)
     *
     * @param string|null $text
     * @param array $options
     * @return void
     */
    public function editMessageText(?string $text = null, array $options = []): void
    {
        try {
            $content = array_merge([
                'text' => $text ?? $this->Callback_Message_Text()
            ], $this->setCallbackContentMessage($options));

            $this->telegram->editMessageText($content);
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
    }

    /**
     * Edit message reply markup from a telegram
     * (Edit message reply markup only)
     *
     * @param array $options
     * @return void
     */
    public function editMessageReplyMarkup(array $options = []): void
    {
        try {
            $this->telegram->editMessageReplyMarkup($this->setCallbackContentMessage($options));
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
    }

    /**
     * Get the text from callback message
     *
     * @return string
     */
    public function Callback_Message_Text(): string
    {
        return $this->telegram->Callback_Message()['text'];
    }

    /**
     * Create content for a callback message
     *
     * @param array $options
     * @return array
     */
    public function setCallbackContentMessage(array $options = []): array
    {
        $content = array(
            'chat_id' => $this->telegram->Callback_ChatID(),
            'message_id' => $this->telegram->MessageID(),
            'disable_web_page_preview' => true,
            'parse_mode' => 'HTML',
        );

        $content['reply_markup'] = $options['reply_markup'] ? $this->telegram->buildInlineKeyBoard($options['reply_markup']) : null;

        return $content;
    }

    /**
     * Generate menu markup
     *
     * @return array[]
     */
    public function menuMarkup(): array
    {
        return [
            [
                $this->telegram->buildInlineKeyBoardButton("📰 About", "", "about", ""),
                $this->telegram->buildInlineKeyBoardButton("📞 Contact", config('author.contact'))
            ], [
                $this->telegram->buildInlineKeyBoardButton("💠 Source Code", config('author.source_code'))
            ]
        ];
    }
}

<?php

namespace TelegramGithubNotify\App\Services;

use Telegram;

class TelegramService
{
    public string $token;

    public string $chatId;

    public Telegram $telegram;

    public array $messageData;

    public function __construct()
    {
        $this->setToken();
        $this->setChatId();
        $this->storeByToken();
        $this->getDataOfMessage();
    }

    /**
     * @return void
     */
    private function setToken(): void
    {
        $this->token = config('telegram-bot.token');
    }

    /**
     * @return void
     */
    private function setChatId(): void
    {
        $this->chatId = config('telegram-bot.chat_id');
    }

    /**
     * @return void
     */
    private function storeByToken(): void
    {
        $this->telegram = new Telegram($this->token);
    }

    /**
     * @return void
     */
    private function getDataOfMessage(): void
    {
        $this->messageData = $this->telegram->getData() ?? [];
    }

    /**
     * Send callback response to telegram
     *
     * @param string|null $text
     * @return void
     */
    public function telegramToolHandler(string $text = null): void
    {
        switch ($text) {
            case '/start':
                $reply = view('tools.start', ['first_name' => $this->telegram->FirstName()]);
                $this->sendMessage($reply, ['photo' => curl_file_create(config('app.image'), 'image/png')], 'Photo');
                break;
            case '/help':
                $replyMarkup = [
                    [
                        $this->telegram->buildInlineKeyBoardButton("📰 About", "", "about", ""),
                        $this->telegram->buildInlineKeyBoardButton("📞 Contact", config('author.contact'))
                    ],
                    [$this->telegram->buildInlineKeyBoardButton("💠 Source Code", config('author.source_code'))]
                ];
                $this->sendMessage(view('tools.help'), ['reply_markup' => $replyMarkup]);
                break;
            case '/token':
            case '/id':
            case '/usage':
            case '/server':
                $this->sendMessage(view('tools.' . trim($text, '/')));
                break;
            default:
                $this->sendMessage('🤨 Invalid Request!');
        }
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
        } elseif ($sendType === 'Photo' && !empty($options)) {
            $content['photo'] = $options['photo'];
            $content['caption'] = $message;
        }

        if (!empty($options) && isset($options['reply_markup'])) {
            $content['reply_markup'] = $this->telegram->buildInlineKeyBoard($options['reply_markup']);
        }

        $this->telegram->{'send' . $sendType}($content);
    }

    /**
     * Send callback response to telegram
     *
     * @param string|null $callback
     * @return void
     */
    protected function sendCallbackResponse(string $callback = null): void
    {
        if (!empty($callback) && $callback == 'about') {
            $reply = view('tools.about');
            $content = array(
                'callback_query_id' => $this->telegram->Callback_ID(),
                'text' => $reply,
                'show_alert' => true
            );
            $this->telegram->answerCallbackQuery($content);
        }
    }

    /**
     * Check callback from a telegram
     *
     * @return bool
     */
    public function checkCallback(): bool
    {
        if (!is_null($this->telegram->Callback_ChatID())) {
            $callback = $this->telegram->Callback_Data();
            $this->sendCallbackResponse($callback);

            return true;
        }

        return false;
    }
}

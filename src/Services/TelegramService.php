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
                $reply = view('tools.start', [
                    'first_name' => $this->telegram->FirstName()
                ]);
                $content = array(
                    'chat_id' => $this->chatId,
                    'photo' => curl_file_create('public/images/github.jpeg', 'image/png'),
                    'caption' => $reply,
                    'disable_web_page_preview' => true,
                    'parse_mode' => 'HTML'
                );
                $this->telegram->sendPhoto($content);

                break;
            case '/help':
                $option = [
                    [
                        $this->telegram->buildInlineKeyBoardButton("📰 About", "", "about", ""),
                        $this->telegram->buildInlineKeyBoardButton("📞 Contact", "https://t.me/tannp27")
                    ],
                    [
                        $this->telegram->buildInlineKeyBoardButton(
                            "💠 Source Code",
                            "https://github.com/lbiltech/telegram-bot-github-notify"
                        ),
                    ]
                ];
                $reply = view('tools.help');
                $content = array(
                    'chat_id' => $this->chatId,
                    'reply_markup' => $this->telegram->buildInlineKeyBoard($option),
                    'text' => $reply,
                    'disable_web_page_preview' => true,
                    'parse_mode' => 'HTML'
                );
                $this->telegram->sendMessage($content);

                break;
            case '/id':
                $reply = "Your id is <code>{$this->chatId}</code>";
                $content = array(
                    'chat_id' => $this->chatId,
                    'text' => $reply,
                    'disable_web_page_preview' => true,
                    'parse_mode' => 'HTML'
                );
                $this->telegram->sendMessage($content);

                break;
            case '/server':
                $reply = view('tools.server');
                $content = array(
                    'chat_id' => $this->chatId,
                    'text' => $reply,
                    'disable_web_page_preview' => true,
                    'parse_mode' => 'HTML'
                );
                $this->telegram->sendMessage($content);

                break;
            case '/token':
                $reply = "This bot token is: <code>{$this->token}</code>";
                $content = array(
                    'chat_id' => $this->chatId,
                    'text' => $reply,
                    'disable_web_page_preview' => true,
                    'parse_mode' => 'HTML'
                );
                $this->telegram->sendMessage($content);

                break;
            case '/usage':
                $reply = view('tools.usage');
                $content = array(
                    'chat_id' => $this->chatId,
                    'text' => $reply,
                    'disable_web_page_preview' => true,
                    'parse_mode' => 'HTML'
                );
                $this->telegram->sendMessage($content);

                break;
            default:
                $reply = "🤨 Invalid Request";
                $content = array('chat_id' => $this->chatId, 'text' => $reply);

                $this->telegram->sendMessage($content);
        }
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

<?php

namespace TelegramGithubNotify\App\Services;

use Symfony\Component\HttpFoundation\Request;
use Telegram;

class TelegramService
{
    public string $token;

    public string $chatId;

    public Telegram $telegram;

    public array $messageData;

    protected Request $request;

    public function __construct()
    {
        $this->setToken();
        $this->setChatId();
        $this->storeByToken();
        $this->getDataOfMessage();

        $this->request = Request::createFromGlobals();
    }

    /**
     * @return void
     */
    public function setToken(): void
    {
        $this->token = config('telegram-bot.token');
    }

    /**
     * @return void
     */
    public function setChatId(): void
    {
        $this->chatId = config('telegram-bot.chat_id');
    }

    /**
     * @return Telegram
     */
    public function storeByToken(): Telegram
    {
        $this->telegram = new Telegram($this->token);
        return $this->telegram;
    }

    /**
     * @return void
     */
    public function getDataOfMessage(): void
    {
        $this->messageData = $this->telegram->getData() ?? [];
    }

    /**
     * @param string $text
     * @return void
     */
    public function telegramToolHandler(string $text = ''): void
    {
        switch ($text) {
            case '/start':
                $img = curl_file_create('img/github.jpeg', 'image/png');
                $reply = "<b>🙋🏻 " . config('app.name') . " 🤓</b>\n\nHey <b>{$this->telegram->FirstName()}</b>,\n\nI can send you notifications from your GitHub Repository instantly to your Telegram. use /help for more information about me";
                $content = array(
                    'chat_id' => $this->chatId,
                    'photo' => $img,
                    'caption' => $reply,
                    'disable_web_page_preview' => true,
                    'parse_mode' => "HTML"
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
                            "https://github.com/tanhongit/telegram-bot-github-notify"
                        ),
                    ]
                ];
                $reply = "<b>Available Commands </b>\n\n/id - To get chat id\n/host - To get Host Address\n/help - To show this Message\n/usage - How to use me\n\nSelect a command :";
                $content = array(
                    'chat_id' => $this->chatId,
                    'reply_markup' => $this->telegram->buildInlineKeyBoard($option),
                    'text' => $reply,
                    'disable_web_page_preview' => true,
                    'parse_mode' => "HTML"
                );

                $this->telegram->sendMessage($content);
                break;
            case '/id':
                $reply = "Your id is <code>{$this->chatId}</code>";
                $content = array(
                    'chat_id' => $this->chatId,
                    'text' => $reply,
                    'disable_web_page_preview' => true,
                    'parse_mode' => "HTML"
                );

                $this->telegram->sendMessage($content);
                break;
            case '/host':
                $reply = "Server Address : <a href=\"{$_SERVER['REMOTE_ADDR']}\">{$_SERVER['REMOTE_ADDR']}</a>";
                $content = array(
                    'chat_id' => $this->chatId,
                    'text' => $reply,
                    'disable_web_page_preview' => true,
                    'parse_mode' => "HTML"
                );

                $this->telegram->sendMessage($content);
                break;
            case '/usage':
                $reply = "<b>Adding webhook (Website Address) to your GitHub repository</b>\n\n 1) Redirect to <i>Repository Settings->Set Webhook->Add Webhook</i> \n 2) Set your Payload URL\n 3) Set content type to \"<code>application/x-www-form-urlencoded</code>\"\n\n <b>Thats it. you will receive all notifications through me 🤗</b>";
                $content = array(
                    'chat_id' => $this->chatId,
                    'text' => $reply,
                    'disable_web_page_preview' => true,
                    'parse_mode' => "HTML"
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
     * @param string|null $callback
     * @return void
     */
    public function sendCallbackResponse(string $callback = null): void
    {
        if (!empty($callback) && $callback == 'about') {
            $reply = "Thanks for using our bot. \n\n The bot is designed to send notifications based on GitHub events from your github repo instantly to your Telegram account.";
            $content = array(
                'callback_query_id' => $this->telegram->Callback_ID(),
                'text' => $reply,
                'show_alert' => true
            );
            $this->telegram->answerCallbackQuery($content);
        }
    }
}

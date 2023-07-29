<?php

namespace TelegramGithubNotify\App\Services;

class TelegramService extends AppService
{
    public array $messageData;

    public SettingService $settingService;

    public function __construct()
    {
        parent::__construct();

        $this->messageData = $this->telegram->getData() ?? [];
        $this->settingService = new SettingService();
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
                $this->sendMessage(view('tools.help'), ['reply_markup' => $this->helpMarkup()]);
                break;
            case '/token':
            case '/id':
            case '/usage':
            case '/server':
                $this->sendMessage(view('tools.' . trim($text, '/')));
                break;
            case '/settings':
                $this->sendMessage(view('tools.settings'), ['reply_markup' => $this->settingService->settingMarkup(0)]);
                break;
            default:
                $this->sendMessage('🤨 Invalid Request!');
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

    /**
     * @return array[]
     */
    public function helpMarkup(): array
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

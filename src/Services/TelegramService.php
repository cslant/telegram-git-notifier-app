<?php

namespace TelegramGithubNotify\App\Services;

use TelegramGithubNotify\App\Models\Setting;

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
            case '/menu':
                $this->sendMessage(view('tools.menu'), ['reply_markup' => $this->menuMarkup()]);
                break;
            case '/token':
            case '/id':
            case '/usage':
            case '/server':
                $this->sendMessage(view('tools.' . trim($text, '/')));
                break;
            case '/settings':
                $this->settingService->settingHandle();
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
    private function sendCallbackResponse(string $callback = null): void
    {
        if (empty($callback)) {
            return;
        }

        if ($callback === 'about') {
            $this->answerCallbackQuery(view('tools.about'));
        } elseif (str_contains($callback, Setting::SETTING_PREFIX)) {
            $this->settingService->settingCallbackHandler($callback);
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
            $this->sendCallbackResponse($this->telegram->Callback_Data());
            return true;
        }
        return false;
    }
}

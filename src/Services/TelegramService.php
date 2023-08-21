<?php

namespace TelegramNotificationBot\App\Services;

use TelegramNotificationBot\App\Models\Setting;

class TelegramService extends AppService
{
    public const MENU_COMMANDS = [
        [
            'command' => '/start',
            'description' => 'Welcome to the bot'
        ], [
            'command' => '/menu',
            'description' => 'Show menu of the bot'
        ], [
            'command' => '/token',
            'description' => 'Show token of the bot'
        ], [
            'command' => '/id',
            'description' => 'Show the ID of the current chat'
        ], [
            'command' => '/usage',
            'description' => 'Show step by step usage'
        ], [
            'command' => '/server',
            'description' => 'To get Server Information'
        ], [
            'command' => '/settings',
            'description' => 'Show settings of the bot'
        ],
    ];

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
     * @param string $text
     * @return void
     */
    public function telegramToolHandler(string $text = '/start'): void
    {
        set_time_limit(60);
        switch ($text) {
            case '/start':
                $this->sendStartMessage();
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
            case '/set_menu':
                $this->setMyCommands();
                break;
            default:
                $this->sendMessage('🤨 Invalid Request!');
        }
    }

    /**
     * Send the welcome message to a telegram
     *
     * @return void
     */
    public function sendStartMessage(): void
    {
        $reply = view(
            'tools.start',
            ['first_name' => $this->telegram->FirstName()]
        );
        $this->sendMessage(
            $reply,
            ['photo' => curl_file_create(config('app.image'), 'image/png')],
            'Photo'
        );
    }

    /**
     * Set the menu button for a telegram
     *
     * @return void
     */
    public function setMyCommands(): void
    {
        $this->telegram->setMyCommands([
            'commands' => json_encode(self::MENU_COMMANDS)
        ]);
        $this->sendMessage(view('tools.set_menu'));
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
        if (!is_null($this->telegram->getData()) && !is_null($this->telegram->Callback_ChatID())) {
            $this->sendCallbackResponse($this->telegram->Callback_Data());
            return true;
        }
        return false;
    }
}

<?php

namespace LbilTech\TelegramGitNotifierApp\Services;

use LbilTech\TelegramGitNotifier\Models\Event;
use LbilTech\TelegramGitNotifier\Models\Setting;
use LbilTech\TelegramGitNotifierApp\Traits\SetFilePathTrait;
use LbilTech\TelegramGitNotifier\Services\SettingService as BaseSettingService;
use Telegram;

class SettingService extends BaseSettingService
{
    use SetFilePathTrait;

    public function __construct(
        Telegram $telegram,
        Setting $setting,
        Event $event,
        ?string $chatId = null
    ) {
        parent::__construct($telegram, $setting, $event, $chatId);
    }
}

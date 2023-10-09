<?php

namespace LbilTech\TelegramGitNotifierApp\Traits;

use LbilTech\TelegramGitNotifier\Models\Event;
use LbilTech\TelegramGitNotifier\Models\Setting;

trait SetFilePathTrait
{
    /**
     * @param Event $event
     * @param string $flatForm
     *
     * @return Event
     */
    public function setEventByFlatForm(
        Event $event,
        string $flatForm
    ): Event {
        $flatFormFiles = [
            'github' => __DIR__ . '/../../storage/json/github-event.json',
            'gitlab' => __DIR__ . '/../../storage/json/gitlab-event.json',
        ];

        $event->setPlatformFile($flatFormFiles[$flatForm]);
        $event->setEventConfig($flatForm);

        return $event;
    }

    /**
     * @param Setting $setting
     * @param string|null $settingFile
     *
     * @return Setting
     */
    public function setSettingFile(Setting $setting, string $settingFile = null): Setting
    {
        $setting->setSettingFile(
            $settingFile ?? __DIR__ . '/../../storage/json/tg-setting.json'
        );

        return $setting;
    }
}

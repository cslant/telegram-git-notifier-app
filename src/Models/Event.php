<?php

namespace TelegramGithubNotify\App\Models;

class Event
{
    public const EVENT_FILE = __DIR__ . '/../../storage/tg-event.json';

    public const EVENT_PREFIX = Setting::SETTING_PREFIX . '.event.';

    public array $eventConfig = [];

    public function __construct()
    {
        if (file_exists(self::EVENT_FILE)) {
            $this->setEventConfig();
        }
    }

    /**
     * Set event config
     *
     * @return void
     */
    public function setEventConfig(): void
    {
        $json = file_get_contents(self::EVENT_FILE);
        $this->eventConfig = json_decode($json, true);
    }

    /**
     * Get event config
     *
     * @return array
     */
    public function getEventConfig(): array
    {
        return $this->eventConfig;
    }
}

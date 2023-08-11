<?php

namespace TelegramGithubNotify\App\Models;

class Event
{
    public const EVENT_FILE = __DIR__ . '/../../storage/tg-event.json';

    public const EVENT_PREFIX = Setting::SETTING_CUSTOM_EVENTS . '.evt.';

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
    private function setEventConfig(): void
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

    /**
     * Update event config by event and action
     *
     * @param string $event
     * @param string|null $action
     * @return void
     */
    public function updateEvent(string $event, string|null $action): void
    {
        if (!empty($action)) {
            $this->eventConfig[$event][$action] = !$this->eventConfig[$event][$action];
        } else {
            $this->eventConfig[$event] = !$this->eventConfig[$event];
        }

        $this->saveEventConfig();
    }

    /**
     * Save event config
     *
     * @return void
     */
    private function saveEventConfig(): void
    {
        if (file_exists(self::EVENT_FILE)) {
            $json = json_encode($this->eventConfig, JSON_PRETTY_PRINT);
            file_put_contents(self::EVENT_FILE, $json, LOCK_EX);
        }
    }
}

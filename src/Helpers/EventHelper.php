<?php

namespace TelegramGithubNotify\App\Helpers;

class EventHelper
{
    public array $eventConfig = [];

    public function __construct()
    {
        if (file_exists(__DIR__ . '/../../storage/tg-event.json')) {
            $this->loadEventConfig();
        }
    }

    /**
     * Load event config
     *
     * @return void
     */
    public function loadEventConfig(): void
    {
        $json = file_get_contents(__DIR__ . '/../../storage/tg-event.json');
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

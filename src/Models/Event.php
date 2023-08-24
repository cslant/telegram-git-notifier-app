<?php

namespace TelegramNotificationBot\App\Models;

class Event
{
    public const GITHUB_EVENT_FILE = __DIR__ . '/../../storage/json/github-event.json';

    public const GITLAB_EVENT_FILE = __DIR__ . '/../../storage/json/gitlab-event.json';

    public const PLATFORM_FILES = [
        'github' => self::GITHUB_EVENT_FILE,
        'gitlab' => self::GITLAB_EVENT_FILE,
    ];

    public const DEFAULT_PLATFORM = 'github';

    public const EVENT_PREFIX = Setting::SETTING_CUSTOM_EVENTS . '.evt.';

    public const GITHUB_EVENT_SEPARATOR = 'gh.';

    public const GITLAB_EVENT_SEPARATOR = 'gl.';

    public const EVENT_HAS_ACTION_SEPARATOR = 'atc.';

    public const EVENT_UPDATE_SEPARATOR = '.upd';

    public const PLATFORM_EVENT_SEPARATOR = [
        'github' => self::GITHUB_EVENT_SEPARATOR,
        'gitlab' => self::GITLAB_EVENT_SEPARATOR,
    ];

    public array $eventConfig = [];

    private string $platform = self::DEFAULT_PLATFORM;

    public function __construct()
    {
        if (file_exists(self::PLATFORM_FILES[$this->platform])) {
            $this->setEventConfig();
        }
    }

    /**
     * Set event config
     *
     * @param string $platform
     * @return void
     */
    public function setEventConfig(string $platform = self::DEFAULT_PLATFORM): void
    {
        $this->platform = $platform;

        $json = file_get_contents(self::PLATFORM_FILES[$this->platform]);
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
        $jsonFile = self::PLATFORM_FILES[$this->platform];
        if (file_exists($jsonFile)) {
            $json = json_encode($this->eventConfig, JSON_PRETTY_PRINT);
            file_put_contents($jsonFile, $json, LOCK_EX);
        }
    }
}

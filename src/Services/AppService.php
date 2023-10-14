<?php

namespace LbilTech\TelegramGitNotifierApp\Services;

use LbilTech\TelegramGitNotifierApp\Traits\SetFilePathTrait;
use LbilTech\TelegramGitNotifier\Services\AppService as BaseAppService;
use Telegram;

class AppService extends BaseAppService
{
    use SetFilePathTrait;

    public function __construct(Telegram $telegram = null)
    {
        parent::__construct($telegram);
    }
}

<?php

namespace LbilTech\TelegramGitNotifierApp\Services;

use LbilTech\TelegramGitNotifierApp\Traits\SetFilePathTrait;
use LbilTech\TelegramGitNotifier\Services\AppService as BaseAppService;

class AppService extends BaseAppService
{
    use SetFilePathTrait;

    public function __construct() {
        parent::__construct();
    }
}

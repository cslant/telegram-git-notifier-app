<?php

use LbilTech\TelegramGitNotifier\Exceptions\InvalidViewTemplateException;
use LbilTech\TelegramGitNotifier\Exceptions\SendNotificationException;
use LbilTech\TelegramGitNotifierApp\Http\Actions\IndexAction;

require __DIR__ . '/init.php';

$indexAction = new IndexAction();

try {
    $indexAction();
} catch (InvalidViewTemplateException|SendNotificationException $e) {
    error_log($e->getMessage());
}

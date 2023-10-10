<?php

use LbilTech\TelegramGitNotifier\Exceptions\InvalidViewTemplateException;
use LbilTech\TelegramGitNotifier\Exceptions\SendNotificationException;
use LbilTech\TelegramGitNotifierApp\Http\Actions\SendNotificationAction;

require __DIR__ . '/init.php';

$sendNotifyAction = new SendNotificationAction();

try {
    $sendNotifyAction();
} catch (InvalidViewTemplateException|SendNotificationException $e) {
    error_log($e->getMessage());
}

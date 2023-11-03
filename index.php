<?php

use CSlant\TelegramGitNotifier\Exceptions\EntryNotFoundException;
use CSlant\TelegramGitNotifier\Exceptions\InvalidViewTemplateException;
use CSlant\TelegramGitNotifier\Exceptions\MessageIsEmptyException;
use CSlant\TelegramGitNotifier\Exceptions\SendNotificationException;
use CSlant\TelegramGitNotifierApp\Http\Actions\IndexAction;

require __DIR__ . '/init.php';

$indexAction = new IndexAction();

try {
    $indexAction();
} catch (InvalidViewTemplateException|SendNotificationException|EntryNotFoundException|MessageIsEmptyException $e) {
    error_log($e->getMessage());
}

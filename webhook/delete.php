<?php

use CSlant\TelegramGitNotifier\Exceptions\WebhookException;
use CSlant\TelegramGitNotifierApp\Http\Actions\WebhookAction;

require __DIR__ . '/../init.php';

$webhookAction = new WebhookAction();

try {
    echo $webhookAction->delete();
} catch (WebhookException $e) {
    echo $e->getMessage();
}

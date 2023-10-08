<?php

use LbilTech\TelegramGitNotifierApp\Http\Actions\SendNotifyAction;

require __DIR__ . '/init.php';

$sendNotifyAction = new SendNotifyAction();
$sendNotifyAction();

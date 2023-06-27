<?php
/**
 * @var $payload mixed
 */

$message = "♻️ <b>Connection Successful</b>\n\n";

if (isset($payload->organization)) {
    $message .= "Organization: <b>{$payload->organization->login}</b>\n";
}

if (isset($payload->repository)) {
    $message .= "Repository: <b>{$payload->repository->full_name}</b>\n";
}

if (isset($payload->sender)) {
    $message .= "Sender (triggered the event): <b>{$payload->sender->login}</b>\n";
}

echo $message;

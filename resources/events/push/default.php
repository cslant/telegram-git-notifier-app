<?php
/**
 * @var $payload mixed
 */

$count = count($payload->commits);
$noun = ($count > 1) ? "commits" : "commit";

$message = "⚙️ <b>{$count}</b> new {$noun} to <b>{$payload->repository->name}:{$payload->repository->default_branch}</b>\n\n";

foreach ($payload->commits as $commit) {
$commitId = substr($commit->id, -7);
$this->message .= "<a href=\"{$commit->url}\">{$commitId}</a>: {$commit->message} by <i>{$commit->author->name}</i>\n";
}

$this->message .= "\nPushed by : <b>{$payload->pusher->name}</b>\n";

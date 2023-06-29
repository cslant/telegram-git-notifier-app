<?php
/**
 * @var $payload mixed
 */

if ($payload->workflow_run->conclusion === 'success') {
    $message = "🎉 <b>Workflow Completed</b> from <a href=\"{$payload->repository->html_url}\">{$payload->repository->full_name}</a>\n\n";

    $message .= "Done workflow: 🎉 <b>{$payload->workflow_run->name}</b> ✨ \n\n";
} else {
    $message = "🚫 <b>Canceled Workflow</b> from <a href=\"{$payload->repository->html_url}\">{$payload->repository->full_name}</a>\n\n";

    $message .= "Failed workflow: 🚫 <b>{$payload->workflow_run->name}</b> ❌ \n\n";
}

// $message .= "📤 Commit: <b>{$payload->workflow_run->head_commit->message}</b>\n\n";

$message .= "🔗 Link: <a href=\"{$payload->workflow_run->html_url}\">{$payload->workflow_run->html_url}</a>\n\n";

echo $message;

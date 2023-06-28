<?php
/**
 * @var $payload mixed
 */

$message = '';
if (count($payload->pull_request->requested_reviewers) > 0) {
    $reviewers = [];
    foreach ($payload->pull_request->requested_reviewers as $reviewer) {
        $reviewers[] = $reviewer->login;
    }

    $message .= "👥 Reviewers: " . implode(', ', $reviewers) . "\n";
}

return $message;

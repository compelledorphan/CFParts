<?php
use CallFire\Common\Subscription;
require 'vendor/autoload.php';

$postData = file_get_contents('php://input'); // Collect raw input data
if(!($format = Subscription::is_event_request())) {
    // This request does not conform to what an event notification looks like
    exit;
}
if(!($event = Subscription::event($postData, $format))) {
    // This is not an event
    exit;
}

switch(true) {
    case $event instanceof Subscription\TextNotification:
        $text = $event->getText();
        $fromNumber = $text->getFromNumber();
        $message = $text->getMessage();
        $created = $text->getCreated();
        $logMessage = "[{$created}] {$fromNumber}: {$message}";
        
        file_put_contents(__DIR__.'/text-messages.log', $logMessage.PHP_EOL, FILE_APPEND);
        break;
    case $event instanceof Subscription\CallFinished:
        $call = $event->getCall();
        $fromNumber = $text->getFromNumber();
        $created = $text->getCreated();
        $logMessage = "[{$created}] {$fromNumber}";
        
        file_put_contents(__DIR__.'/finished-calls.log', $logMessage.PHP_EOL, FILE_APPEND);
        break;
    default:
        throw new Exception('Unknown event type');
}

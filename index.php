<?php

require('autoloader.php');

$reports = new Maropost\Api\Reports(1000, 'wTX-esFcYzEMjSLEqkdWgGcf8yR7osiROc9uU-CjJXQDxMshn_SM-Q');

// gets Opens with all api documented examples
$res = $reports->getOpens(
    ['first_name', 'email', 'city'],
    null,
    null,
    true,
    null,
    null,
    2
);
var_dump($res->getData());

// gets Clicks with all api documented examples
$res = $reports->getClicks([], null, null, null, null, null, 2);
var_dump($res->getData());

// gets Bounces with all api documented examples
$res = $reports->getBounces([], null, null, null, null, null, 'hard', 2);
var_dump($res->getData());

// gets Unsubscribes with all api documented examples
$res = $reports->getUnsubscribes([], null, null, null, null, null, 2);
var_dump($res->getData());

// gets AbReports with all api documented examples
$res = $reports->getUnsubscribes([], null, null, null, null, null, 2);
var_dump($res->getData());

// gets Journeys with all api documented examples
$res = $reports->getAbReports('', null, null, 2);
var_dump($res->getData());

// gets Journeys with all api documented examples
$res = $reports->getJourneys();
var_dump($res->getData());
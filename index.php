<?php

require('autoloader.php');

$reports = new Maropost\Api\Reports(1000, 'wTX-esFcYzEMjSLEqkdWgGcf8yR7osiROc9uU-CjJXQDxMshn_SM-Q');
$campaigns = new Maropost\Api\AbTestCampaigns(1000, 'wTX-esFcYzEMjSLEqkdWgGcf8yR7osiROc9uU-CjJXQDxMshn_SM-Q');

// gets Opens with all api documented examples
$res = $reports->getOpens(['fields' => 'email,first_name,city,last_name', 'from' => '2016-06-28', 'to' => '2019-04-11', 'unique' => true, 'per' => 3]);
var_dump($res->getData());

// gets Clicks with all api documented examples
$res = $reports->getClicks(['fields' => 'email,first_name,city,last_name', 'from' => '2016-06-28', 'to' => '2019-04-11', 'unique' => true, 'per' => 3]);
var_dump($res->getData());

// gets Bounces with all api documented examples
$res = $reports->getBounces(['fields' => 'email,first_name,city,last_name', 'from' => '2016-06-28', 'to' => '2019-04-11', 'unique' => true, 'per' => 3, 'type' => 'hard']);
var_dump($res->getData());

// gets Unsubscribes with all api documented examples
$res = $reports->getUnsubscribes(['fields' => 'email,first_name,city,last_name', 'from' => '2016-06-28', 'to' => '2019-04-11', 'unique' => true, 'per' => 3]);
var_dump($res->getData());

// gets AbReports with all api documented examples
$res = $reports->getUnsubscribes(['fields' => 'email,first_name,city,last_name', 'from' => '2016-06-28', 'to' => '2019-04-11', 'unique' => true, 'per' => 3]);
var_dump($res->getData());

// gets Journeys with all api documented examples
$res = $reports->getAbReports(['fields' => 'email,first_name,city,last_name', 'from' => '2016-06-28', 'to' => '2019-04-11', 'unique' => true, 'per' => 3]);
var_dump($res->getData());

<?php

require('autoloader.php');

$reports = new Maropost\Api\Reports(1000, 'wTX-esFcYzEMjSLEqkdWgGcf8yR7osiROc9uU-CjJXQDxMshn_SM-Q');
$campaigns = new Maropost\Api\AbTestCampaigns(1000, 'wTX-esFcYzEMjSLEqkdWgGcf8yR7osiROc9uU-CjJXQDxMshn_SM-Q');

// gets Opens with all api documented examples
$res = $reports->getOpens(['fields' => 'email,first_name,city,last_name', 'from' => '2016-06-28', 'to' => '2019-04-11', 'unique' => true, 'per' => 3]);

var_dump($res->getData());

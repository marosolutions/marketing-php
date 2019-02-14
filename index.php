<?php

require('autoloader.php');

$reports = new Maropost\Api\Reports(1000, 'wTX-esFcYzEMjSLEqkdWgGcf8yR7osiROc9uU-CjJXQDxMshn_SM-Q');
$campaigns = new Maropost\Api\AbTestCampaigns(1000, 'wTX-esFcYzEMjSLEqkdWgGcf8yR7osiROc9uU-CjJXQDxMshn_SM-Q');

//$res = $reports->get('reports', ['per' => 10]);
$minimalCampaign = [
    'name' => 'TestAbCampaignPHPapi',
    'from_emal' => 'testab@campaign.com',
    'reply_to'  => 'replyto@campaign.com',
    'address'   => 'Bagdle,Lalitpur',
    'language'  => 'en'
];
$res = $campaigns->create($minimalCampaign);

var_dump($res);

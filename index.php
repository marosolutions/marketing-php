<?php

require('autoloader.php');

function dd()
{
    foreach (func_get_args() as $arg) {
        echo '<pre>';
        echo print_r($arg, true);
        echo '</pre>';
    }

    die;
}

/*
 * Reports Api
 *
 */

/*

$reports = new Maropost\Api\Reports(1000, 'wTX-esFcYzEMjSLEqkdWgGcf8yR7osiROc9uU-CjJXQDxMshn_SM-Q');

var_dump($reports->getOpens(
    ['first_name', 'email', 'city'],
    null,
    null,
    true,
    null,
    null,
    2
));
var_dump($reports->getClicks([], null, null, null, null, null, 2));
var_dump($reports->getBounces([], null, null, null, null, null, 'hard', 2));
var_dump($reports->getUnsubscribes([], null, null, null, null, null, 2));
var_dump($reports->getUnsubscribes([], null, null, null, null, null, 2));
var_dump($reports->getAbReports('', null, null, 2));
var_dump($reports->getJourneys());

*/

/*
 * Campaigns Api
 *
 *

$campaign = new Maropost\Api\Campaigns(1000, 'wTX-esFcYzEMjSLEqkdWgGcf8yR7osiROc9uU-CjJXQDxMshn_SM-Q');

var_dump($campaign->getComplaintReports(5));
var_dump($campaign->getUnsubscribeReports(5));
var_dump($campaign->getHardBounceReports(5));
var_dump($campaign->getSoftBounceReports(5));
var_dump($campaign->getBounceReports(6));
var_dump($campaign->getLinkReports(5, true));
var_dump($campaign->getClickReports(5));
var_dump($campaign->getOpenReports(4, true));
var_dump($campaign->getDeliveredReports(4));
var_dump($campaign->get());die;

*/

/*
Ab Test Campaign API

$abTestCamp = new Maropost\Api\AbTestCampaigns(1000, 'wTX-esFcYzEMjSLEqkdWgGcf8yR7osiROc9uU-CjJXQDxMshn_SM-Q');
$groupAttr = [
    [
        'name'  => 'Group A',
        'content_id'    => "92",
        'subject'       => 'test subject',
        'from_name'     => 'test from 1',
        'preheader'     => '232',
        'percentage'    => '5',
    ],
    [
        'name'  => 'Group B',
        'content_id'    => "92",
        'subject'       => 'test subject',
        'from_name'     => 'test from 2',
        'preheader'     => '232',
        'percentage'    => '5',
    ]
];
$createRes = $abTestCamp->createAbTest('Test Campaign test', 'someemail@from.com', 'reply-to@from.com', 'home address', 'en', $groupAttr, date('Y-m-d H:i:s'));

dd($createRes);

*/


/*
Contacts Api
*/

$contacts = new \Maropost\Api\Contacts(1000, 'wTX-esFcYzEMjSLEqkdWgGcf8yR7osiROc9uU-CjJXQDxMshn_SM-Q');

dd($contacts->createOrUpdateContact(10135855,'metowrite@gmail.com', 'updated aashu', 'updated lastname')->getData());
dd($contacts->createOrUpdateForList(1,'writetome@gmail.com', 'aashu', 'acharya', '23453245', '2354324453', 65, ['first_cust_field' => 'test passed'], [], [], false, false));
dd($contacts->getForList(1));
dd($contacts->getClicks(5));
dd($contacts->getOpens(5));
dd($contacts->getForEmail('rohit@gmail.com'));

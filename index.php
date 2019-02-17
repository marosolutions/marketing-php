<?php

require('autoloader.php');


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
 */

$campaign = new Maropost\Api\Campaigns(1000, 'wTX-esFcYzEMjSLEqkdWgGcf8yR7osiROc9uU-CjJXQDxMshn_SM-Q');

var_dump($campaign->getComplaintReports(5));die;
var_dump($campaign->getUnsubscribeReports(5));die;
var_dump($campaign->getHardBounceReports(5));die;
var_dump($campaign->getSoftBounceReports(5));die;
var_dump($campaign->getBounceReports(6));die;
var_dump($campaign->getLinkReports(5, true));die;
var_dump($campaign->getClickReports(5));die;
var_dump($campaign->getOpenReports(4, true));die;
var_dump($campaign->getDeliveredReports(4));die;
var_dump($campaign->get());die;

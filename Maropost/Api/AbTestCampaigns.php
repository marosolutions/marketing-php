<?php

namespace Maropost\Api;

use Maropost\Api\Abstractions\OperationResult;
use Maropost\Api\Abstractions\Api;
use Httpful\Request;

class AbTestCampaigns
{
    use Api;

    public function __construct($accountId, $authToken)
    {
        $this->auth_token = $authToken;
        $this->accountId = $accountId;
        $this->resource = 'campaigns';
    }

    public function createAbTest(
        string $name,
        string $fromEmail,
        string $replyTo,
        string $address,
        string $language,
        array $campaignGroupsAttributes,
        string $sendAt
    ): OperationResult
    {
        $json = $this->buildAbTestCampaign($name, $fromEmail, $replyTo, $address, $language, $campaignGroupsAttributes, $sendAt);

        return $this->_post('ab_test', [], $json);
    }

    private function buildAbTestCampaign(
        string $name,
        string $fromEmail,
        string $replyTo,
        string $address,
        string $language,
        array $campaignGroupsAttributes,
        string $sendAt
    ) : \stdClass
    {
        $abTestCampaign = [
            'name' => $name,
            'from_email' => $fromEmail,
            'reply_to'  => $replyTo,
            'address'   => $address,
            'language'  => $language,
            'campaign_groups_attributes'   => $campaignGroupsAttributes,
            'send_at'   => $sendAt
        ];

        return (object) $abTestCampaign;
    }

}

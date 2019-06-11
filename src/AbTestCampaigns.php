<?php

namespace Maropost\Api;

use Maropost\Api\Abstractions\OperationResult;
use Maropost\Api\Abstractions\Api;
use Httpful\Request;
use Maropost\Api\ResultTypes\GetResult;

/**
 * Class AbTestCampaigns
 * @package Maropost\Api
 */
class AbTestCampaigns
{
    use Api;

    /**
     * AbTestCampaigns constructor.
     * @param $accountId
     * @param $authToken
     */
    public function __construct($accountId, $authToken)
    {
        $this->auth_token = $authToken;
        $this->accountId = $accountId;
        $this->resource = 'campaigns';
    }

    /**
     * Creates an Ab Test Campaign
     *
     * @param string $name
     * @param string $fromEmail
     * @param string $replyTo
     * @param string $address
     * @param string $language Allowed value for language can be either of these:
     * ['en' for English, 'es' for Spanish, 'de' for German, 'it' for Italian, 'fr' for French, 'pt' for Portuguese, 'pl' for Polish, 'da' for Danish, 'zh' for Chinese, 'nl' for Dutch, 'sv' for Swedish, 'no' for Norwegian]
     * @param array $campaignGroupsAttributes
     * @param string $commit Allowed values for commit: 'Save as Draft' or 'Send Test' or 'Schedule'
     * @param \DateTime|null $sendAt
     * @param int|null $brandId
     * @param array $suppressedListIds
     * @param array $suppressedSegmentIds
     * @param array $suppressedJourneyIds
     * @param int|null $emailPreviewLink Allowed values: 1- email the preview link, 0- do not email the preview link
     * @param string|null $decidedBy Allowed values for decided_by: ('TopChoice' for Top Choices) or
     * ('Opens' for Highest Open Rate) or ('Clicks' for Highest Click Rate) or ('Manual' for Manual Selection) or
     * ('click_to_open' for Highest Click-to-Open Rate) or ('conversions' for Highest Conversion Rate)
     * @param array $lists
     * @param array $cTags
     * @param array $segments
     * @return OperationResult
     */
    public function createAbTest(
        string $name,
        string $fromEmail,
        string $replyTo,
        string $address,
        string $language,
        array $campaignGroupsAttributes,
        string $commit,
        \DateTime $sendAt,
        int $brandId = null,
        array $suppressedListIds = [],
        array $suppressedSegmentIds = [],
        array $suppressedJourneyIds = [],
        int $emailPreviewLink = null,
        string $decidedBy = null,
        array $lists = [],
        array $cTags = [],
        array $segments = []
    ): OperationResult
    {
        $object = $this->buildAbTestCampaign(
            $name,
            $fromEmail,
            $replyTo,
            $address,
            $language,
            $campaignGroupsAttributes,
            $commit,
            $sendAt,
            $brandId,
            $suppressedListIds,
            $suppressedSegmentIds,
            $suppressedJourneyIds,
            $emailPreviewLink,
            $decidedBy,
            $lists,
            $cTags,
            $segments
        );

        /*
        * @Todo: this needs to be escalated to the Core Api Team, as there is no documentation to resolve errors like these
        * seems like validation errors, but without resolution guidance in the docs.
        * Winning criteria must be selected
        * Campaign groups can't be less than two.
        * No recipients were selected.
        */

        return $this->_post('ab_test', [], $object);
    }

    /**
     * @param string $name
     * @param string $fromEmail
     * @param string $replyTo
     * @param string $address
     * @param string $language
     * @param array $campaignGroupsAttributes
     * @param string $commit
     * @param \DateTime|null $sendAt
     * @param int|null $brandId
     * @param array $suppressedListIds
     * @param array $suppressedSegmentIds
     * @param array $suppressedJourneyIds
     * @param int|null $emailPreviewLink
     * @param string|null $decidedBy
     * @param array $lists
     * @param array $cTags
     * @param array $segments
     * @return \stdClass
     */
    private function buildAbTestCampaign(
        string $name,
        string $fromEmail,
        string $replyTo,
        string $address,
        string $language,
        array $campaignGroupsAttributes,
        string $commit,
        \DateTime $sendAt = null,
        int $brandId = null,
        array $suppressedListIds = [],
        array $suppressedSegmentIds = [],
        array $suppressedJourneyIds = [],
        int $emailPreviewLink = null,
        string $decidedBy = null,
        array $lists = [],
        array $cTags = [],
        array $segments = []
    ): \stdClass
    {
        $abTestCampaign = [
            'name' => $name,
            'from_email' => $fromEmail,
            'reply_to' => $replyTo,
            'address' => $address,
            'language' => $language,
            'send_at' => $sendAt->format('Y-m-d H:i:s'),
            'commit' => $commit,
            'brand_id' => $brandId,
            'email_preview_link' => $emailPreviewLink,
            'decided_by' => $decidedBy,
        ];
        $abTestCampaign = $this->_discardNullAndEmptyValues($abTestCampaign);

        $paramsToSanitize = [
            'campaign_groups_attributes' => $campaignGroupsAttributes,
            'suppressed_list_ids' => $suppressedListIds,
            'suppressed_segment_ids' => $suppressedSegmentIds,
            'suppressed_journey_ids' => $suppressedJourneyIds,
            'lists' => $lists,
            'ctags' => $cTags,
            'segments' => $segments,
        ];

        foreach ($paramsToSanitize as $key => $param) {
            $param = $this->_discardNullAndEmptyValues($param);

            if (!empty($param)) {
                $abTestCampaign[$key] = (object)$param;
            }
        }

        return (object)$abTestCampaign;
    }

}

<?php

namespace Maropost\Api;

use Httpful\Request;
use Maropost\Api\Abstractions\Api;
use Maropost\Api\ResultTypes\GetResult;
use Maropost\Api\Abstractions\OperationResult;

class TransactionalCampaigns
{
    use Api;

	public function __construct($accountId, $authStr)
	{
		$this->auth_token = $authStr;
		$this->accountId = $accountId;
        $this->resource = 'transactional_campaigns';
	}

    /**
     * Gets the list of Transaction Campaigns.
     *
     * @return GetResult
     */
	public function get() : GetResult
	{
        try {
            return $this->_get('', []);
        } catch (\Exception $e) {
            die('exception ');
        }
	}

    /**
     * Creates a Transactional Campaign
     *
     * @param string $name campaign name
     * @param string $subject campaign subject
     * @param string $preheader campaign preheader
     * @param string $fromName sender name in the email
     * @param string $fromEmail sender email address
     * @param string $replyTo reply-to email address
     * @param int $contentId
     * @param bool $emailPreviewLink
     * @param string $address
     * @param string $language ISO 639-1 language code
     * @param string ...$ctags array of campaign tags
     * @return OperationResult
     */
	public function create(
        string $name,
        string $subject,
        string $preheader,
        string $fromName,
        string $fromEmail,
        string $replyTo,
        int $contentId,
        bool $emailPreviewLink,
        string $address,
        string $language,
        string... $ctags
    ) : OperationResult
	{
	    $campaign = (object)[
	      "name" => $name,
          "subject" => $subject,
          "preheader" => $preheader,
          "from_name" => $fromName,
          "from_email" => $fromEmail,
          "reply_to" => $replyTo,
          "content_id" => strval($contentId),
          "email_preview_link" => $emailPreviewLink,
          "address" => $address,
          "language" => $language,
          "add_ctags" => $ctags
        ];
	    $object = (object)["campaign" => $campaign];
	    $result = $this->_post("", [], $object);
		return $result;
	}

    /**
     * Sends a transactional campaign email to a recipient. Sender's information will be automatically fetched from the
     * transactional campaign, unless provided in the function arguments.
     *
     * @param int $campaignId must be a campaign that exists when you call .get()
     * @param int $contentId If provided, the transactional campaign's content will be replaced by this content.
     * @param string $contentName If $contentId is null, the transactional campaign's content name will be replaced by this name.
     * @param string $contentHtmlPart If $contentId is null, the transactional campaign's content HTML part will be replaced by this HTML part.
     * @param string $contentTextPart If $contentId is null, the transactional campaign's content Text part will be replaced by this Text part.
     * @param int $sendAtHour Must be 1-12. Otherwise the email will go out immediately. If the hour is less than the current hour, the email will go out the following day.
     * @param int $sendAtMinute Must be 0-60. Otherwise will be treated as 0. If the hour and minute combine to less than the current time, the email will go out the following day.
     * @param bool $ignoreDnm If true, ignores the Do Not Mail list for the recipient contact.
     * @param int $contactId contact ID of the recipient.
     * @param string $recipientEmail email address. Ignored if $contactId > 0.
     * @param string $recipientFirstName recipient's first name. Ignored if $contactId > 0.
     * @param string $recipientLastName recipient's last name. Ignored if $contactId > 0.
     * @param string $bccEmail BCC recipient. May only pass a single email address, or empty string.
     * @param string $fromName sender's name. If $fromEmail is set, it overrides the transactional campaign default sender name. Ignored otherwise.
     * @param string $fromEmail sender's email address. Overrides the transactional campaign default sender email.
     * @param string $subject subject line of email. Overrides the transactional campaign default subject.
     * @param string $replyTo reply-to address. Overrides the transactional campaign default reply-to.
     * @param string $senderAddress physical address of sender. Overrides the transactional campaign default sender address.
     * @param array $tags a list of key/value pair objects where the key is the name of the tag within the content, and the value is the tag's replacement upon sending.
     * @param string ...$ctags array of campaign tags.
     * @return OperationResult
     */
	public function sendEmail(
	    int $campaignId,
        int $contentId,
        string $contentName,
        string $contentHtmlPart,
        string $contentTextPart,
        int $sendAtHour,
        int $sendAtMinute,
        bool $ignoreDnm,
        int $contactId,
        string $recipientEmail,
        string $recipientFirstName,
        string $recipientLastName,
        string $bccEmail,
        string $fromName,
        string $fromEmail,
        string $subject,
        string $replyTo,
        string $senderAddress,
        array $tags,
        string... $ctags
    ) : OperationResult
	{
	    $array = [
	        "campaign_id" => $campaignId
        ];
	    if ($contentId > 0) {
	        $array["content_id"] = $contentId;
        }
	    else {
	        $array["content"] = (object)array(
	            "name" => $contentName,
                "html_part" => $contentHtmlPart,
                "text_part" => $contentTextPart
            );
        }
	    if ($contactId > 0) {
	        $array["contact_id"] = $contactId;
        }
	    else {
	        $array["contact"] = (object)array(
	            "email" => $recipientEmail,
                "first_name" => $recipientFirstName,
                "last_name" => $recipientLastName
            );
	        // TODO: add city & other custom fields.
        }
	    if ($sendAtHour > 0 && $sendAtHour <= 12) {
	        if (!($sendAtMinute >= 0 && $sendAtMinute <= 60)) {
	            $sendAtMinute = 0;
            }
	        $array["send_time"] = (object)array(
	            "hour" => strval($sendAtHour),
                "minute" => strval($sendAtMinute)
            );
        }
	    if ($ignoreDnm) {
	        $array["ignore_dnm"] = true;
        }
	    if (strlen($fromEmail) != 0) {
	        $array["from_email"] = $fromEmail;
	        $array["from_name"] = $fromName;
        }
	    if (strlen($replyTo) != 0) {
	        $array["reply_to"] = $replyTo;
        }
	    if (strlen($subject) != 0) {
	        $array["subject"] = $subject;
        }
	    if (strlen($senderAddress) != 0) {
	        $array["address"] = $senderAddress;
        }
	    if (strlen($bccEmail) != 0) {
	        $array["bcc"] = $bccEmail;
        }
	    if (sizeof($tags) != 0) {
	        if (!is_array($tags)) {
                return new GetResult(null, "Provided 'tags' array is not actually an array.");
            }
	        foreach ($tags as $key => $value) {
	            if (!is_string($key)) {
                    return new GetResult(null, "All keys in your tags array must be strings.");
                }
	            if (is_null($value)) {
                    return new GetResult(null, "All values in your tags array must be non-null.");
                }
            }
	        $array["tags"] = $tags;
        }
	    if (sizeof($ctags) != 0) {
	        $array["add_ctags"] = $ctags;
        }

	    // TODO: add Content Form Structured Data

	    $object = (object)$array;
		$result = $this->_post("deliver", [], $object);
		return $result;
	}
}
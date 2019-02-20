# Maropost API PHP Package

## Summary
This package provides programmatic access to several Maropost services. It 
consists of eight services within the `Maropost.Api` namespace. Each service 
consists of one or more functions that perform an operation against your 
Maropost account. These functions return a result object indicating 
success/failure, any Exceptions throw, and the resulting data.

## Usage
To use a service, first instantiate it, providing your Maropost AccountId
and Auth Token. For example, to get your list of reports using the Reports
service, execute:

    $reports = new Maropost.Api.Reports($myAccountId, $myAuthToken);
    $result = $reports.get();
    if ($result.isSuccess()) {
        $myReports = $result.getData();
    }

The result object contains three fields:

- `isSuccess` boolean
- `errorMessage` string
- `exception` Exception

If `isSuccess` is `false`, then `errorMessage` will contain information, and
`exception` *might* contain an exception, depending upon the reason for
failure. If there is an exception, then `isSuccess` will always be false.

The object also contains one function, `getData()`, which contains whatever
data the operation itself provides. Some operations, such as `delete()`
operations, might not provide any data.

## Specific APIs
The specific APIs contained are:

- [Campaigns](#campaigns)
- [AB Test Campaigns](#ab-test-campaigns)
- [Transactional Campaigns](#transactional-campaigns)
- [Contacts](#contacts)
- [Journeys](#journeys)
- [Product and Revenue](#product-and-revenue)
- [Relational Tables](#relational-tables)
- [Reports](#reports)

### Campaigns
#### Instantiation:

    new Maropost.Api.Campaigns($myAccountId, $myAuthToken)

#### Available methods:

 - `get()`
   - returns the list of campaigns for the account
 - `getBounceReports(int $id)`
   - returns the list of bounce reports for the given campaign ID
 - `getClickReports(int $id, bool unique = null)`
   - returns the list of click reports for the given campaign ID
   - `$unique`: `true` = get for unique contacts. Otherwise, `false`. 
 - `getComplaintReports(int $id)`
   - returns the list of complaint reports for the given campaign ID
 - `getDeliveredReports(int $id)`
   - returns the list of delivered reports for the given campaign ID
   - `$unique`: `true` = get for unique contacts. Otherwise, `false`. 
 - `getHardBounceReports(int $id)`
   - returns the list of hard bounces for the given campaign ID
 - `getLinkReports(int $id, bool unique = null)`
   - returns the list of link reports for the given campaign ID
   - `$unique`: `true` = get for unique contacts. Otherwise, `false`. 
 - `getOpenReports(int $id, bool unique = null)`
   - returns the list of open reports for the given campaign ID
   - `$unique`: `true` = get for unique contacts. Otherwise, `false`. 
 - `getSoftBounceReports(int $id)`
   - returns the list of soft bounce reports for the given campaign ID
 - `getUnsubscribeReports(int $id)`
   - returns the list of unsubscribe reports for the given campaign ID
   
### AB Test Campaigns
#### Instantiation:

    new Maropost.Api.AbTestCampaigns($myAccountId, $myAuthToken)

#### Available Methods:
 - `createAbTest(string $name, string $fromEmail, string $replyTo,
            string $address, string $language, array $campaignGroupsAttributes,
            string $sendAt)`
   - `$name`: name of the new campaign
   - `$fromEmail`: default sender email address for campaign emails
   - `$replyTo`: default reply-to email address for campaign emails
   - `$address`: default physical address included on campaign emails
   - `$language`: ISO 639-1 language code (e.g, `"en"`). 2 characters.
   - `$campaignGroupsAttributes`: array of attributes. Each attribute is
   itself an object with the following properties (all strings):
     - `name`: name of the group
     - `content_id`: content ID
     - `subject`: subject line of emails
     - `from_name`: "from name" on emails
     - `percentage`: percentage of emails that should be sent with these
     settings.
   - `$sendAt`: DateTime string having the format  YYYY-MM-DDTHH:MM:SS-05:00

### Transactional Campaigns

#### Instantiation:

    new Maropost.Api.TransactionalCampaigns($myAccountId, $myAuthToken)

#### Available methods:
 - `get()`
     * returns the list of Transaction Campaigns
 - `create(string $name, string $subject, string $preheader,
        string $fromName, string $fromEmail, string $replyTo, 
        int $contentId, bool $emailPreviewLink, string $address,
        string $language, string... $ctags)`
     * Creates a Transactional Campaign
     * `$name` campaign name
     * `$subject` campaign subject
     * `$preheader` campaign preheader
     * `$fromName` sender name in the email
     * `$fromEmail` sender email address
     * `$replyTo` reply-to email address
     * `$contentId`
     * `$emailPreviewLink`
     * `$address` physical address
     * `$language` ISO 639-1 language code
     * `...$ctags` array of campaign tags

 - `sendEmail(int $campaignId, 
        int $contentId = null,
        string $contentName = null,
        string $contentHtmlPart = null,
        string $contentTextPart = null,
        int $sendAtHour = null,
        int $sendAtMinute = null,
        bool $ignoreDnm = null,
        int $contactId = null,
        string $recipientEmail = null,
        string $recipientFirstName = null,
        string $recipientLastName = null,
        array $recipientCustomFields = null,
        string $bccEmail = null,
        string $fromName = null,
        string $fromEmail = null,
        string $subject = null,
        string $replyTo = null,
        string $senderAddress = null,
        array $tags = null,
        array $ctags = null
    )`
     * Sends a transactional campaign email to a recipient. Sender's 
     information will be automatically fetched from the transactional 
     campaign, unless provided in the function arguments.
     * `$campaignId`: must be a campaign that already exists when you call `$svc->get()`. If you don't have one, first call `$svc->create()`.
     * `$contentId`: If provided, the transactional campaign's content will be replaced by this content.
     * `$contentName`: If $contentId is null, the transactional campaign's content name will be replaced by this name.
     * `$contentHtmlPart`: If $contentId is null, the transactional campaign's content HTML part will be replaced by this HTML part.
     * `$contentTextPart`: If $contentId is null, the transactional campaign's content Text part will be replaced by this Text part.
     * `$sendAtHour`: Must be 1-12. Otherwise the email will go out immediately. If the hour is less than the current hour, the email will go out the following day.
     * `$sendAtMinute`: Must be 0-60. Otherwise will be treated as 0. If the hour and minute combine to less than the current time, the email will go out the following day.
     * `$ignoreDnm`: If true, ignores the Do Not Mail list for the recipient contact.
     * `$contactId`: contact ID of the recipient.
     * `$recipientEmail`: email address. Ignored unless `$contactId` is null. Otherwise, it must be a well-formed email address according to `FILTER_VALIDATE_EMAIL`.
     * `$recipientFirstName`: recipient's first name. Ignored unless `$contactId` is null.
     * `$recipientLastName`: recipient's last name. Ignored unless `$contactId` is null.
     * `$recipientCustomFields`: custom fields for the recipient. Ignored unless `$contactId` is null. Is an associative array where the item key is the name of the custom field, and the item value is the field value. All keys must be strings. All values must be non-null scalars.
     * `$bccEmail`: BCC recipient. May only pass a single email address, empty string, or null. If provided, it must be a well-formed email address according to `FILTER_VALIDATE_EMAIL`.
     * `$fromName`: sender's name. If `$fromEmail` is set, it overrides the transactional campaign default sender name. Ignored otherwise.
     * `$fromEmail`: sender's email address. Overrides the transactional campaign default sender email.
     * `$subject`: subject line of email. Overrides the transactional campaign default subject.
     * `$replyTo`: reply-to address. Overrides the transactional campaign default reply-to.
     * `$senderAddress`: physical address of sender. Overrides the transactional campaign default sender address.
     * `$tags`: associative array where the item key is the name of the tag within the content, and the item value is the tag's replacement upon sending. All keys must be strings. All values must be non-null scalars.
     * `$ctags`: campaign tags. Must be a simple array of scalar values.
     

### Contacts

#### Instantiation:

    new Maropost.Api.Contacts($myAccountId, $myAuthToken)

#### Available methods:

* TODO

### Journeys

#### Instantiation:

    new Maropost.Api.Journeys($myAccountId, $myAuthToken)

#### Available methods:

 - `get()`
     * Gets the list of journeys

 - `getCampaigns(int $journeyId)`
     * Gets the list of all campaigns for the specified journey

 - `getContacts(int $journeyId)`
     * Gets the list of all contacts for the specified journey

 - `stopAll(int $contactId, string $recipientEmail, string $uid)`
     * Stops all journeys, filtered for the matching parameters
     * `$contactId`: this filter ignored unless greater than 0
     * `$recipientEmail`: this filter ignored if null
     * `$uid`: this filter ignored if null

 - `pauseJourneyForContact(int $journeyId, int $contactId)`
     * Pause the specified journey for the specified contact

 - `pauseJourneyForUid(int $journeyId, string $uid)`
     * Pause the specified journey for the contact having the specified UID

 - `resetJourneyForContact(int $journeyId, int $contactId)`
     * Reset the specified journey for the specified active/paused contact. 
     Resetting a contact to the beginning of the journeys will result in 
     sending of the same journey campaigns as originally sent.

 - `public function resetJourneyForUid(int $journeyId, string $uid)`
     * Reset the specified journey for the active/paused contact having the 
     specified UID. Resetting a contact to the beginning of the journeys 
     will result in sending of the same journey campaigns as originally sent.

 - `public function startJourneyForContact(int $journeyId, int $contactId)`
     * Restarts a journey for a paused contact. Adds a new contact in 
     journey. Retriggers the journey for a contact who has finished its 
     journey once. (To retrigger, *make sure* that "Retrigger Journey" option 
     is enabled.)

 - `startJourneyForUid(int $journeyId, string $uid)`
     * Restarts a journey for a paused contact having the specified UID. 
     Adds a new contact in journey. Retriggers the journey for a contact 
     who has finished its journey once. (To retrigger, *make sure* that 
     "Retrigger Journey" option is enabled.)

### Product and Revenue

#### Instantiation:

    new Maropost.Api.ProductAndRevenue($myAccountId, $myAuthToken)

#### Available methods:

 - `getOrder(int $id)`
     * Gets a the specified order.
     * `$id`: matches the original_order_id field of the order

 - `createOrder(bool $requireUnique, string $contactEmail, string $contactFirstName, string $contactLastName,
        array $customFields, array $addTags, array $removeTags, string $uid, string $listIds, string $orderDateTime,
        string $orderStatus, string $originalOrderId, string $grandTotal, int $campaignId = null,
        string $couponCode = null, OrderItemInput... $orderItems)`
     * Creates an order
     * `$requireUnique`: true to validate that the order has a unique original_order_id for the given contact.
     * `$contactEmail`
     * `$contactFirstName`
     * `$contactLastName`
     * `$customFields`: associative array where the key (string) represents the field name and the value is the field value
     * `$addTags`: simple array of tags to add (scalar values)
     * `$removeTags`: simple array of tags to remove (scalar values)
     * `$uid`
     * `$listIds`
     * `$orderDateTime`: uses the format: YYYY-MM-DDTHH:MM:SS-05:00
     * `$orderStatus`
     * `$originalOrderId`
     * `$grandTotal`
     * `$campaignId`
     * `$couponCode`
     * `$orderItems`
    
 - `deleteForOriginalOrderId(int $originalOrderId)`
     * Deletes the complete eCommerce order if the order is cancelled or 
     returned
     * `$originalOrderId` matches the original_order_id field of the order

 - `deleteForOrderId(int $id)`
     * Deletes the complete eCommerce order if the order is cancelled or 
     returned using Maropost order id
     * `$id`: Maropost order_id

 - `deleteProductsForOriginalOrderId(int $originalOrderId, int... $productIds)`
     * Deletes the specified product(s) from a complete eCommerce order if 
     the product(s) is cancelled or returned
     * `$originalOrderId`: matches the original_order_id field of the order
     * `$productIds`: the product(s) to delete from the order

 - `deleteProductsForOrderId(int $id, int... $productIds)`
     * Deletes the specified product(s) from a complete eCommerce order if 
     the product(s) is cancelled or returned
     * `$id`: Maropost order_id
     * `$productIds`: the product(s) to delete from the order

### Relational Tables

#### Instantiation:
Unlike the other services, the constructor for this requires a third
parameter: `$tableName`. So for example:

    $svc = new Maropost.Api.RelationalTables($myAccountId, $myAuthToken, $tableName);

`$tableName` sets which relational table the service's operations should act against.
To switch tables, you do not need to re-instantiate the service. Instead,
you can call

    $svc->_setTableName($newTableName);

You can also call `_getTableName()` to determine which table is currently
set.

#### Available functions:

 - `get()`
     * Gets the records of the Relational Table

 - `show(int $id)`
     * Gets the specified record from the Relational Table
     * `$id`: ID of the existing record you wish to read

 - `create(KeyValue... $keyValues)`
     * Adds a record to the Relational Table
     * `$keyValues`: Array of `Maropost.Api.InputTypes.KeyValue` objects,
     each item consisting of two fields:
       - `$key`: string representing the name of the field
       - `$value`: scalar value representing the new value for the field

 - `update(KeyValue... $keyValues)`
     * Updates the given fields of a record in the Relational Table
     * `$keyValues`: Array of `Maropost.Api.InputTypes.KeyValue` objects,
     each item consisting of two fields:
       - `$key`: string representing the name of the field
       - `$value`: scalar value representing the new value for the field
       - NOTE: One of the KeyValues must represent the unique identifier.

 - `delete(int $id)`
     * Deletes the given record of the Relational Table
     * `$id`: ID of the Relational Table to delete

### Reports

#### Instantiation:

    new Maropost.Api.Reports($myAccountId, $myAuthToken)

#### Available methods:
 - `get()`
   - returns the list of reports

 - `getOpens(
        array $fields = [],
        string $from = null,
        string $to = null,
        bool $unique = null,
        string $email = null,
        string $uid = null,
        int $per = null
    )`
     * returns the list of open reports based on filters and fields provided
     * `$fields`: contact field names to retrieve
     * `$from`: the beginning of date range filter
     * `$to`: the end of the date range filter
     * `$unique`: when true, gets only unique opens
     * `$email`: filters by provided email in the contact
     * `$uid`: filters by uid
     * `$per`: determines how many records per request to receive
 - `getClicks(
        array $fields = [],
        string $from = null,
        string $to = null,
        bool $unique = null,
        string $email = null,
        string $uid = null,
        int $per = null
    )`
     * returns the list of click reports
     * `$fields`: plucks these contact fields if they exist
     * `$from`: start of specific date range filter
     * `$to`: end of date range filter
     * `$unique`: if true, gets unique records
     * `$email`: gets Clicks for specific email
     * `$uid`: gets Clicks for provided uid
     * `$per`: gets the specified number of records

 - `getBounces(
        array $fields = [],
        string $from = null,
        string $to = null,
        bool $unique = null,
        string $email = null,
        string $uid = null,
        string $type = null,
        int $per = null
    )`
     * returns the list of bounce reports
     * `$fields`: plucks these contact fields if they exist
     * `$from`: start of specific date range filter
     * `$to`: end of date range filter
     * `$unique`: if true, gets unique records
     * `$email`: gets Bounces for specific email
     * `$uid`: gets Bounces for provided uid
     * `$per`: gets the specified number of records

 - `getUnsubscribes(
        array $fields = [],
        string $from = null,
        string $to = null,
        bool $unique = null,
        string $email = null,
        string $uid = null,
        int $per = null
    )`
     * returns the list of Unsubscribes with provided filter constraints
     * `$fields`: plucks these contact fields if they exist
     * `$from`: start of specific date range filter
     * `$to`: end of date range filter
     * `$unique` if true, gets unique records
     * `$email` gets Unsubscribes for specific email
     * `$uid` gets Unsubscribes for provided uid
     * `$per` gets the specified number of records

 - `getComplaints(
        array $fields = [],
        string $from = null,
        string $to = null,
        bool $unique = null,
        string $email = null,
        string $uid = null,
        int $per = null
    )`
     * returns the list of complaints filtered by provided params
     * `$fields`: plucks these contact fields if they exist
     * `$from`: start of specific date range filter
     * `$to`: end of date range filter
     * `$unique`: if true, gets unique records
     * `$email`: gets Complaints for specific email
     * `$uid`: gets Complaints for provided uid
     * `$per`: gets the specified number of records

 - `getAbReports(
        string $name = '',
        string $from = null,
        string $to = null,
        int $per = null
    )`
     * returns the list of Ab Reports
     * `$name`: to get ab_reports with mentioned name
     * `$from`: beginning of date range filter
     * `$to`: end of date range filter
     * `$per`: gets the mentioned number of reports

 - `getJourneys()`
     * returns the list of all Journeys

# Coveo Push API SDK for PHP

The Coveo Push API SDK for PHP is meant to help you use the [Coveo Push API](https://docs.coveo.com/en/68/cloud-v2-developers/push-api) when coding in PHP.

This SDK includes the following features:

- Document validation before they are pushed to the plaform
- Source update status before and after a document update
- Automatic push of large files to the platform through an Amazon S3 container

For code examples on how to use the SDK, see the `examples` section.

## Installation

Make sure you have [git](https://git-scm.com/downloads) installed.

Then, in your command prompt, enter the following command:

```
pip install git+https://github.com/coveo-labs/SDK-Push-PHP
```

## Including the SDK in Your Code

Simply add the following lines into your project:

```php
require_once('../coveopush/CoveoConstants.php');
require_once('../coveopush/CoveoDocument.php');
require_once('../coveopush/CoveoPermissions.php');
require_once('../coveopush/CoveoPush.php');
require_once('../coveopush/Enum.php');
```

## Prerequisites

Before pushing a document to a Coveo Cloud organization, you need to ensure that you have a Coveo Cloud organization, and that this organization has a [Push source](https://docs.coveo.com/en/94/cloud-v2-developers/creating-a-push-source).

Once you have those prerequisites, you need to get your Organization Id, Source Id, and API Key. For more information on how to do that, see [Push API Tutorial 1 - Managing Shared Content](https://docs.coveo.com/en/92/cloud-v2-developers/push-api-tutorial-1---managing-shared-content).

You must also create fields and mappings for each metadata you will be sending with your documents. Otherwise, some of the data you push might get ignored by Coveo Cloud. To learn how to create fields and mappings in Coveo Cloud, see [Add/Edit a Field: [FieldName] - Panel](https://docs.coveo.com/en/1982/cloud-v2-administrators/add-edit-a-field-fieldname---panel) and [Edit the Mappings of a Source: [SourceName]](https://docs.coveo.com/en/1640/cloud-v2-administrators/edit-the-mappings-of-a-source-sourcename).

## Pushing Documents

The Coveo Push API supports two methods for pushing data: sending a single document, or sending batches of documents.

Unless you are only sending one document, you should **always** be sending your documents in batches.

### Pushing a Single Document

You should only use this method when you want to add or update a single document. Pushing several documents using this method may lead to the `429 - Too Many Requests` response from the Coveo platform.

Before pushing your document, you should specify the Source Id, Organization Id, and Api Key to use.

```php
$push = new Coveo\SDKPushPHP\Push($sourceId, $orgId, $apiKey);
```

You can then create a document with the appropriate options, as such:

```php
// Create a document
$mydoc = new Coveo\SDKPushPHP\Document("https://myreference.cov.com/&id=TESTME");
// Set plain text
$mydoc->SetData("ALL OF THESE WORDS ARE SEARCHABLE");
// Set FileExtension
$mydoc->FileExtension = ".html";
// Add Metadata
$mydoc->AddMetadata("connectortype", "CSV");
$authors = array();
array_push($authors,"Coveo");
array_push($authors,"R&D");
// rssauthors should be set as a multi-value field in your Coveo Cloud organization
$mydoc->AddMetadata("rssauthors", $authors);
// Set the Title
$mydoc->Title = "THIS IS A TEST";
// Set permissions
$user_email = "wim@coveo.com";
// Create a permission identity
$myperm = new Coveo\SDKPushPHP\PermissionIdentity(Coveo\SDKPushPHP\PermissionIdentityType::User, "", $user_email);
// Set the permissions on the document
$allowAnonymous = True;
$mydoc->SetAllowedAndDeniedPermissions(array($myperm), array(), $allowAnonymous);
```

The above will create a document with the `https://myreference&id=TESTME` URI. It will then set its document text to the value for `SetData`, and add its appropriate metadata.

Once the document is ready, you can push it to your index with the following line:

```php
$push->AddSingleDocument($mydoc);
```

A full example would look like this:

```php
require_once('../coveopush/CoveoConstants.php');
require_once('../coveopush/CoveoDocument.php');
require_once('../coveopush/CoveoPermissions.php');
require_once('../coveopush/CoveoPush.php');
require_once('../coveopush/Enum.php');

//Reads the $sourceId, $orgId, and $apiKey
require_once('config.php');

// Setup the push client
$push = new Coveo\SDKPushPHP\Push($sourceId, $orgId, $apiKey);
// Get a first Ordering Id
$startOrderingId = $push->CreateOrderingId();

// Create a document
$mydoc = new Coveo\SDKPushPHP\Document("https://myreference.cov.com/&id=TESTME");
// Set plain text
$mydoc->SetData("ALL OF THESE WORDS ARE SEARCHABLE");
// Set FileExtension
$mydoc->FileExtension = ".html";
// Add Metadata
$mydoc->AddMetadata("connectortype", "CSV");
$authors = array();
array_push($authors,"Coveo");
array_push($authors,"R&D");
// rssauthors should be set as a multi-value field in your Coveo Cloud organization
$mydoc->AddMetadata("rssauthors", $authors);
// Set the Title
$mydoc->Title = "THIS IS A TEST";
// Set permissions
$user_email = "wim@coveo.com";
// Create a permission identity
$myperm = new Coveo\SDKPushPHP\PermissionIdentity(Coveo\SDKPushPHP\PermissionIdentityType::User, "", $user_email);
// Set the permissions on the document
$allowAnonymous = True;
$mydoc->SetAllowedAndDeniedPermissions(array($myperm), array(), $allowAnonymous);

// Push the document
$push->AddSingleDocument($mydoc);

// Delete older documents
$push->DeleteOlderThan($startOrderingId);
```

### Pushing Batches of Documents

This SDK offers a convenient way to send batches of documents to the Coveo Cloud platform. Using this method, you ensure that your documents do not get throttled when being sent to Coveo Cloud.

As with the previous call, you must first specify your Source Id, Organization Id, and API Key.

```php
$push = new Coveo\SDKPushPHP\Push($sourceId, $orgId, $apiKey);
```

You must then start the batch operation, as well as set the maximum size for each batch. If you do not set a maximum size for your request, it will default to 256 Mb. The size is set in bytes.

```php
// Start the batch
$push->Start($updateSourceStatus, $deleteOlder);
// Set the maximum
$push->SetSizeMaxRequest(150*1024*1024);
```

The `updateSourceStatus` option ensures that the source is set to `Rebuild` while documents are being pushed, while the `deleteOlder` option deletes the documents that were already in your source prior to the new documents you are pushing.

You can then start adding documents to your source, using the `Add` command, as such:

```php
$push->Add(createDoc('/testfiles/Large1.pptx', '1'));
```

For the sake of simplicity, a `createDoc` function is assumed to exist. This function returns documents formatted the same way the `mydoc` element was formatted in the single document example.

The `Add` command checks if the total size of the documents for the current batch does not exceed the maximum size. When it does, it initiates a file upload to Amazon S3, and then pushes this data to Coveo Cloud through the Push API.

Finally, once you are done adding your documents, you should always end the batch operation. This way, the remaining documents will be pushed to the platform, the source status of your Push source will be set back to `Idle`, and old documents will be removed from your source.

The following example demonstrates how to do that.

```php
// Setup the push client
$push = new Coveo\SDKPushPHP\Push($sourceId, $orgId, $apiKey);
// Start the batch
$push->Start($updateSourceStatus, $deleteOlder);
// Set the maximum
$push->SetSizeMaxRequest(150*1024*1024);

$push->Add(createDoc('/testfiles/Large1.pptx', '1'));
$push->Add(createDoc('/testfiles/Large2.pptx', '1'));
$push->Add(createDoc('/testfiles/Large1.pptx', '2'));
$push->Add(createDoc('/testfiles/Large2.pptx', '2'));
$push->Add(createDoc('/testfiles/Large1.pptx', '3'));
$push->Add(createDoc('/testfiles/Large2.pptx', '3'));
$push->Add(createDoc('/testfiles/Large1.pptx', '4'));
$push->Add(createDoc('/testfiles/Large2.pptx', '4'));
$push->Add(createDoc('/testfiles/Large1.pptx', '5'));
$push->Add(createDoc('/testfiles/Large2.pptx', '5'));

# End the Push
$push->End($updateSourceStatus, $deleteOlder);
```

## Adding Securities to Your Documents

In Coveo, you can add securities to documents, so only allowed users or groups can view the document. This SDK allows you to add security provider information with your documents while pushing them. To learn how to format your permissions, see [Push API Tutorial 2 - Managing Secured Content](https://docs.coveo.com/en/98/cloud-v2-developers/push-api-tutorial-2---managing-secured-content).

You should first define your security provider, as such:

```php
// First, define a name for your Security Provider
$mysecprovidername = "MySecurityProviderTest"

// Then, define the cascading security provider information
$cascading = array(
    "Email Security Provider"=> array(
        "name"=> "Email Security Provider",
        "type"=> "EMAIL"));

// Finally, create the provider
$push->AddSecurityProvider($mysecprovidername, "EXPANDED", $cascading);
```

The `AddSecurityProvider` command automatically associates your current source with the newly created security provider.

Once the security provider is created, you can use it to set permissions on your documents.

The folling example adds a simple permission set:

```php
// Set permissions
$user_email = "wim@coveo.com";
// Create a permission identity
$myperm = new Coveo\SDKPushPHP\PermissionIdentity(Coveo\SDKPushPHP\PermissionIdentityType::User, "", $user_email);
// Set the permissions on the document
$allowAnonymous = True;
$mydoc->SetAllowedAndDeniedPermissions(array($myperm), array(), $allowAnonymous);
```

The following example incorporates more complex permission sets to your document, in which users can have access to a document either because they are given access individually, or because they belong to a group who has access to the document. This example also includes users who are specifically denied access to the document.

Finally, this example includes two permissions levels. The first permission level has precedence over the second permission level; a user allowed access to a document in the first permission level but denied in the second level will still have access to the document. However, users that are specifically denied access will still not be able to access the document.

```php
// Define a list of users that should have access to the document.
$users = array("Wim","Peter");

// Define a list of users that should not have access to the document.
$deniedusers = array("Alex","Anne");

// Define a list of groups that should have access to the document.
$groups = array("HR","RD","SALES");

// Create the permission Levels. Each level can include multiple sets.
$permLevel1 = new Coveo\SDKPushPHP\DocumentPermissionLevel('First');
$permLevel1Set1 = new Coveo\SDKPushPHP\DocumentPermissionSet('1Set1');
$permLevel1Set2 = new Coveo\SDKPushPHP\DocumentPermissionSet('1Set2');
$permLevel1Set1->AllowAnonymous = False;
$permLevel1Set2->AllowAnonymous = False;
$permLevel2 = new Coveo\SDKPushPHP\DocumentPermissionLevel('Second');
$permLevel2Set = new Coveo\SDKPushPHP\DocumentPermissionSet('2Set1');
$permLevel2Set->AllowAnonymous = False;

// Set the allowed permissions for the first set of the first level
foreach ($users as $user) {
    // Create the permission identity
    $permLevel1Set1->AddAllowedPermissions(
    new Coveo\SDKPushPHP\PermissionIdentity($GUSER, $mysecprovidername, $user));
}

// Set the denied permissions for the second set of the first level
foreach ($deniedusers as $user) {
  // Create the permission identity
  $permLevel1Set2->AddDeniedPermissions(
  new Coveo\SDKPushPHP\PermissionIdentity($GUSER, $mysecprovidername, $user));
}

// Set the allowed permissions for the first set of the second level
foreach ($groups as $group) {
  // Create the permission identity
  $permLevel2Set->AddAllowedPermissions(
  new Coveo\SDKPushPHP\PermissionIdentity($GGROUP, $mysecprovidername, $group));
}

// Set the permission sets to the appropriate level
$permLevel1->AddPermissionSet($permLevel1Set1);
$permLevel1->AddPermissionSet($permLevel1Set2);
$permLevel2->AddPermissionSet($permLevel2Set);

// Set the permissions on the document
array_push($mydoc->Permissions, $permLevel1);
array_push($mydoc->Permissions, $permLevel2);
```

Securities are created using permission levels, which can hold multiple PermissionSets (see [Complex Permission Model Definition Example](https://docs.coveo.com/en/25/cloud-v2-developers/complex-permission-model-definition-example)).

Setting securities with a custom security provider also requires that you inform the index of which members and user mappings are available. You would normally do that after the indexing process is complete.

## Adding Security Expansion

A batch call is also available for securities.

To do so, you must first start the security expansion, as such:

```php
$push->StartExpansion($mysecprovidername);
```

Any group you have defined in your security must then be properly expanded, as such:

```php
// group memberships for: HR, RD
foreach ($groups as $group) {
    // for each group set the users
    $members = array();
    foreach ($usersingroup as $user) {
        // Create a permission Identity
        array_push($members,
          new Coveo\SDKPushPHP\PermissionIdentityExpansion($GUSER, $mysecprovidername, $user));
    }
    log2($GGROUP);
    log2($group);
    $push->AddExpansionMember(
      new Coveo\SDKPushPHP\PermissionIdentityExpansion($GGROUP, $mysecprovidername, $group), $members, array(), array());
}
```

For each identity, you also need to map it to the email security provider:

```php
foreach ($users as $user) {
    // Create a permission Identity
    $mappings = array();
    array_push($mappings,new Coveo\SDKPushPHP\PermissionIdentityExpansion($GUSER, "Email Security Provider", $user . "@coveo.com"));

    $wellknowns = array();
    array_push($wellknowns, new Coveo\SDKPushPHP\PermissionIdentityExpansion($GGROUP, $mysecprovidername, "Everyone"));

    $push->AddExpansionMapping(
      new Coveo\SDKPushPHP\PermissionIdentityExpansion($GUSER, $mysecprovidername, $user), array(), $mappings, $wellknowns);
}
```

As with the previous batch call, you must remember to end the call, as such:

```php
$push->EndExpansion($mysecprovidername);
```

This way, you ensure that the remaining identities are properly sent to the Coveo Platform.

After the next Security Permission update cycle, the securities will be updated (see [Refresh a Security Identity Provider](https://docs.coveo.com/en/1905/cloud-v2-administrators/security-identities---page#refresh-a-security-identity-provider)).

### Changes

Feb 2021:

- First release

### Dependencies

### References

- [Coveo Push API](https://docs.coveo.com/en/68/cloud-v2-developers/push-api)

### Authors

- [Wim Nijmeijer](https://github.com/wnijmeijer)

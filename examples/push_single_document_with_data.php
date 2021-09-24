<?php
// -------------------------------------------------------------------------------------
// Push Single document with Data property
// -------------------------------------------------------------------------------------

require_once './examples-no-composer.php';
use Coveo\Search\SDK\SDKPushPHP\Document;
use Coveo\Search\SDK\SDKPushPHP\PermissionIdentity;
use Coveo\Search\SDK\SDKPushPHP\PermissionIdentityType;
use Coveo\Search\SDK\SDKPushPHP\Push;

require_once('config.php');

// Setup the push client
$push = new Push($sourceId, $orgId, $apiKey);
// Get a first Ordering Id
$startOrderingId = $push->CreateOrderingId();

// Create a document
$mydoc = new Document("https://myreference.cov.com/&id=TESTME");
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
$myperm = new PermissionIdentity(PermissionIdentityType::User, "", $user_email);
// Set the permissions on the document
$allowAnonymous = True;
$mydoc->SetAllowedAndDeniedPermissions(array($myperm), array(), $allowAnonymous);

// Push the document
$push->AddSingleDocument($mydoc);

// Delete older documents
$push->DeleteOlderThan($startOrderingId);


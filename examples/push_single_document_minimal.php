<?php
// -------------------------------------------------------------------------------------
// Push Single document
// -------------------------------------------------------------------------------------

require_once('../coveopush/CoveoConstants.php');
require_once('../coveopush/CoveoDocument.php');
require_once('../coveopush/CoveoPermissions.php');
require_once('../coveopush/CoveoPush.php');
require_once('../coveopush/Enum.php');

require_once('config.php');
// Setup the push client
$push = new Coveo\SDKPushPHP\Push($sourceId, $orgId, $apiKey);

//$push->UpdateSourceStatus(Coveo\SDKPushPHP\SourceStatusType::Rebuild);


// Create a document
$mydoc = new Coveo\SDKPushPHP\Document("https://myreference.cov.com/doc2");
$mydoc->SetData("This is document Two");
$mydoc->FileExtension = ".html";
$mydoc->AddMetadata("authors", "jdst@coveo.com");
$mydoc->Author = "Wim";
$mydoc->Title = "What's up Doc 2?";
// Push the document
$push->AddSingleDocument($mydoc);



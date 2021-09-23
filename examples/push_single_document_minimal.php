<?php
// -------------------------------------------------------------------------------------
// Push Single document
// -------------------------------------------------------------------------------------

require_once './examples-no-composer.php';
use Coveo\Search\SDK\SDKPushPHP\Document;
use Coveo\Search\SDK\SDKPushPHP\Push;

require_once('config.php');
// Setup the push client
$push = new Push($sourceId, $orgId, $apiKey);

//$push->UpdateSourceStatus(SourceStatusType::Rebuild);


// Create a document
$mydoc = new Document("https://myreference.cov.com/doc2");
$mydoc->SetData("This is document Two");
$mydoc->FileExtension = ".html";
$mydoc->AddMetadata("authors", "jdst@coveo.com");
$mydoc->Author = "Wim";
$mydoc->Title = "What's up Doc 2?";
// Push the document
$push->AddSingleDocument($mydoc);



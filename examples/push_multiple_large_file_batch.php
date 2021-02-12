<?php
// -------------------------------------------------------------------------------------
// Push Multiple large files using the batch api
// -------------------------------------------------------------------------------------

require_once('../coveopush/CoveoConstants.php');
require_once('../coveopush/CoveoDocument.php');
require_once('../coveopush/CoveoPermissions.php');
require_once('../coveopush/CoveoPush.php');
require_once('../coveopush/Enum.php');

require_once('config.php');


function createDoc($myfile){
    // Create a document
    $mydoc = new Coveo\SDKPushPHP\Document('https://www.cov.com/'.$myfile);
    // Get the file contents and compress it
    $mydoc->GetFileAndCompress('..'.$myfile);
    // Set Metadata
    $mydoc->AddMetadata("connectortype", "CSV");
    $mydoc->Title = "THIS IS A TEST ".$myfile;
    // Set permissions
    return $mydoc;
}



$updateSourceStatus = True;
$deleteOlder = True;

// Setup the push client
$push = new Coveo\SDKPushPHP\Push($sourceId, $orgId, $apiKey);
// Create a batch of documents
$batch=array(
    createDoc('/testfiles/BigExample.pdf'),
    createDoc('/testfiles/BigExample2.pptx'));

// Push the documents
$push->AddDocuments($batch, array(), $updateSourceStatus, $deleteOlder);

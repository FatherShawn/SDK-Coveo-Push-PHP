<?php
// -------------------------------------------------------------------------------------
// Push documents using the Start/End Batch method
// -------------------------------------------------------------------------------------

require_once('../coveopush/CoveoConstants.php');
require_once('../coveopush/CoveoDocument.php');
require_once('../coveopush/CoveoPermissions.php');
require_once('../coveopush/CoveoPush.php');
require_once('../coveopush/Enum.php');

require_once('config.php');


function createDoc($myfile,$version){
    // Create a document
    echo "<BR>";
    echo 'Adding '.$myfile.' Version '.$version;
    $mydoc = new Coveo\SDKPushPHP\Document('https://www.cov.com/'.$myfile.'-'.$version);
    // Get the file contents and compress it
    $mydoc->GetFileAndCompress('..'.$myfile);
    // Set Metadata
    $mydoc->AddMetadata("connectortype", "CSV");
    $mydoc->Title = "THIS IS A TEST ".$myfile.' Version '.$version;
    // Set permissions
    return $mydoc;
}



$updateSourceStatus = True;
$deleteOlder = True;

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

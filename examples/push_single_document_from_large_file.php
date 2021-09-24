<?php
// -------------------------------------------------------------------------------------
// Push Single, large document from a filestore
// Automatically a AWS s3 Upload will be retrieved, the file will be uploaded and be pushed
// -------------------------------------------------------------------------------------

require_once './examples-no-composer.php';
use Coveo\Search\SDK\SDKPushPHP\Document;
use Coveo\Search\SDK\SDKPushPHP\PermissionIdentity;
use Coveo\Search\SDK\SDKPushPHP\PermissionIdentityType;
use Coveo\Search\SDK\SDKPushPHP\Push;


require_once('config.php');

// Setup the push client
$push = new Push($sourceId, $orgId, $apiKey);


$myfile = '../testfiles/BigExample.pdf';
$myfilename = 'https://cov.com/BigExample.pdf';
// Create a document
$mydoc = new Document($myfilename);
// Get the file contents and add it to the document
$mydoc->GetFileAndCompress($myfile);
// Set the metadata
$mydoc->AddMetadata("connectortype", "PDF");
// Set the title
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

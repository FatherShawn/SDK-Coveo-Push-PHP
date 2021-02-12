<?php
// -------------------------------------------------------------------------------------
// Push Single, large document from a filestore
// Automatically a AWS s3 Upload will be retrieved, the file will be uploaded and be pushed
// -------------------------------------------------------------------------------------

require_once('../coveopush/CoveoConstants.php');
require_once('../coveopush/CoveoDocument.php');
require_once('../coveopush/CoveoPermissions.php');
require_once('../coveopush/CoveoPush.php');
require_once('../coveopush/Enum.php');

require_once('config.php');

// Setup the push client
$push = new Coveo\SDKPushPHP\Push($sourceId, $orgId, $apiKey);


$myfile = '../testfiles/BigExample.pdf';
$myfilename = 'https://cov.com/BigExample.pdf';
// Create a document
$mydoc = new Coveo\SDKPushPHP\Document($myfilename);
// Get the file contents and add it to the document
$mydoc->GetFileAndCompress($myfile);
// Set the metadata
$mydoc->AddMetadata("connectortype", "PDF");
// Set the title
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

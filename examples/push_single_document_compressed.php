<?php
// -------------------------------------------------------------------------------------
// Push single document using CompressedBinaryData
// -------------------------------------------------------------------------------------

require_once './examples-no-composer.php';
use Coveo\Search\SDK\SDKPushPHP\Push;
use Coveo\Search\SDK\SDKPushPHP\Document;

require_once('config.php');

// Setup the push client
$push = new Push($sourceId, $orgId, $apiKey);

// Get a first Ordering Id
$startOrderingId = $push->CreateOrderingId();

// Create a document
$mydoc = new Document('https://myreference.cov.com/&id=TESTME');
// Set the content. This will also be available as the quickview.
$content = "<meta charset='UTF-16'><meta http-equiv='Content-Type' content='text/html; charset=UTF-16'><html><head><title>My First Title</title><style>.datagrid table { border-collapse: collapse; text-align: left; } .datagrid {display:table !important;font: normal 12px/150% Arial, Helvetica, sans-serif; background: //fff; overflow: hidden; border: 1px solid //006699; -webkit-border-radius: 3px; -moz-border-radius: 3px; border-radius: 3px; }.datagrid table td, .datagrid table th { padding: 3px 10px; }.datagrid table thead th {background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, //006699), color-stop(1, //00557F) );background:-moz-linear-gradient( center top, //006699 5%, //00557F 100% );filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='//006699', endColorstr='//00557F');background-color://006699; color://FFFFFF; font-size: 15px; font-weight: bold; border-left: 1px solid //0070A8; } .datagrid table thead th:first-child { border: none; }.datagrid table tbody td { color: //00496B; border-left: 1px solid //E1EEF4;font-size: 12px;font-weight: normal; }.datagrid table tbody  tr:nth-child(even)  td { background: //E1EEF4; color: //00496B; }.datagrid table tbody td:first-child { border-left: none; }.datagrid table tbody tr:last-child td { border-bottom: none; }</style></head><body style='Font-family:Arial'><div class='datagrid'><table><tbody><tr><td>FirstName</td><td>Willem</td></tr><tr><td>MiddleName</td><td>Van</td></tr><tr><td>LastName</td><td>Post</td></tr><tr><td>PositionDescription</td><td>VP Engineering</td></tr><tr><td>JobFunction</td><td>CTO</td></tr><tr><td>JobFamily</td><td>Management</td></tr></tbody></table></div></body></html>";
$mydoc->SetContentAndZLibCompress($content);
// Set the metadata
$mydoc->AddMetadata("connectortype", "CSV");
// Set the title
$mydoc->Title = "THIS IS A TEST";
// Add a user email to be used for identities

// Push the document
$push->AddSingleDocument($mydoc);

// Delete older documents
$push->DeleteOlderThan($startOrderingId);


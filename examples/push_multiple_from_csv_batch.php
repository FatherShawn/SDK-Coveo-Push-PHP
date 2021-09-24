<?php
// -------------------------------------------------------------------------------------
# Push Multiple From CSV using BATCH calls
// -------------------------------------------------------------------------------------

require_once './examples-no-composer.php';
use Coveo\Search\SDK\SDKPushPHP\Push;
use Coveo\Search\SDK\SDKPushPHP\Document;

require_once('config.php');


function createDoc($post,$id){
    // Create a document
    echo json_encode($post);
    $mydoc = new Document('https://myreference.cov.com/&store=Wim&id='.$post['UserName'].$id);
    $content = "<meta charset='UTF-16'><meta http-equiv='Content-Type' content='text/html; charset=UTF-16'><html><head><title>".$post['FirstName']." ".$post['LastName']." (".$post['JobFunction'].")</title><style>.datagrid table { border-collapse: collapse; text-align: left; } .datagrid {display:table !important;font: normal 12px/150% Arial, Helvetica, sans-serif; background: #fff; overflow: hidden; border: 1px solid #006699; -webkit-border-radius: 3px; -moz-border-radius: 3px; border-radius: 3px; }.datagrid table td, .datagrid table th { padding: 3px 10px; }.datagrid table thead th {background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #006699), color-stop(1, #00557F) );background:-moz-linear-gradient( center top, #006699 5%, #00557F 100% );filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#006699', endColorstr='#00557F');background-color:#006699; color:#FFFFFF; font-size: 15px; font-weight: bold; border-left: 1px solid #0070A8; } .datagrid table thead th:first-child { border: none; }.datagrid table tbody td { color: #00496B; border-left: 1px solid #E1EEF4;font-size: 12px;font-weight: normal; }.datagrid table tbody  tr:nth-child(even)  td { background: #E1EEF4; color: #00496B; }.datagrid table tbody td:first-child { border-left: none; }.datagrid table tbody tr:last-child td { border-bottom: none; }</style></head><body style='Font-family:Arial'><div class='datagrid'><table><tbody><tr><td>FirstName</td><td>".$post[      'FirstName']."</td></tr><tr><td>MiddleName</td><td>".$post['MiddleName']."</td></tr><tr><td>LastName</td><td>".$post['LastName']."</td></tr><tr><td>PositionDescription</td><td>".$post['PositionDescription']."</td></tr><tr><td>JobFunction</td><td>".$post['JobFunction']."</td></tr><tr><td>JobFamily</td><td>".$post['JobFamily']."</td></tr></tbody></table></div></body></html>";
    $mydoc->SetContentAndZLibCompress($content);
    // Set Metadata
    $mydoc->FileExtension = ".html";
    # Set the date
    $date = new DateTime();
    $mydoc->AddMetadata('aboutme','All about '.$post['UserName']);
    $mydoc->AddMetadata('cc',$post['UserName']);
    $myaccountcontent = array();
    for ($x = 0; $x <= 5; $x++) {
      $acc='ACC'.$x;
      $dateacc = new DateTime();
      $dateacc->modify("-".rand(5, 15)." day");
      $myaccountcontent[$acc]=$dateacc->format(DateTime::ATOM);
    }
    $mydoc->AddMetadata('myaccountdate',$myaccountcontent);
    $mydoc->SetDate($date);
    $mydoc->SetModifiedDate($date);
    $mydoc->Title = $post['FirstName'].' ' . $post['LastName'].' '.'('.$post['JobFunction'].')';
    // Set permissions
    return $mydoc;
}



$updateSourceStatus = True;
$deleteOlder = True;
// Setup the push client
// Setup the push client
$push = new Push($sourceId, $orgId, $apiKey);
// Create a batch of documents
$batch=array();
$columns = array();
$first=True;

$csv = array_map(function($v){return str_getcsv($v, ";");}, file('People.csv'));
array_walk($csv, function(&$a) use ($csv) {
  global $batch;
  $a = array_combine($csv[0], $a);
  if ($first) {
    $first=False;
  } else {
    for ($x = 1; $x <= 5; $x++) {
      array_push($batch, createDoc($a,$x));
    }
  }
});

// Push the documents
$push->AddDocuments($batch, array(), $updateSourceStatus, $deleteOlder);

// Wait a bit, now remove
sleep(25);
//function RemoveSingleDocument(string $p_DocumentId, bool $updateStatus=null, int $orderingId=null, bool $deleteChildren=null){

//$push->RemoveSingleDocument('https://myreference.cov.com/&id=UserName',null,null,True);
$push->RemoveSingleDocument('https://myreference.cov.com/&store=Wim',null,null,True);

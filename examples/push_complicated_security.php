<?php
// -------------------------------------------------------------------------------------
// Push single document with complicated security
// -------------------------------------------------------------------------------------

require_once './examples-no-composer.php';

use Coveo\Search\SDK\SDKPushPHP\DefaultLogger;
use Coveo\Search\SDK\SDKPushPHP\Push;
use Coveo\Search\SDK\SDKPushPHP\Document;
use Coveo\Search\SDK\SDKPushPHP\PermissionIdentityType;
use Coveo\Search\SDK\SDKPushPHP\DocumentPermissionLevel;
use Coveo\Search\SDK\SDKPushPHP\DocumentPermissionSet;
use Coveo\Search\SDK\SDKPushPHP\PermissionIdentity;
use Coveo\Search\SDK\SDKPushPHP\PermissionIdentityExpansion;

$logger = new DefaultLogger();
require_once('config.php');

// Shortcut for constants
$GGROUP = PermissionIdentityType::Group;
$GUSER =  PermissionIdentityType::User;

$logger->LogWindow('Make sure that your API KEY has the rights to modify Security Providers !!!!');
$logger->LogWindow('Make sure that your Push Source has Security: Determined by source permissions set');
// Setup the push client
$push = new Push($sourceId, $orgId, $apiKey, NULL, $logger);

// First set the securityprovidername
$mysecprovidername = "MySecurityProviderTest";
// Define cascading security provider information
$cascading = array(
    "Email Security Provider"=> array(
        "name"=> "Email Security Provider",
        "type"=> "EMAIL"));

// Create it
$push->AddSecurityProvider($mysecprovidername, "EXPANDED", $cascading);
$startOrderingId = $push->CreateOrderingId();
// Delete all old entries
$push->DeletePermissionsOlderThan($mysecprovidername, $startOrderingId);
$logger->LogWindow("Old ids removed. Updating security cache");
//sleep(25);

// Create a document
$mydoc = new Document("https://myreference.cov.com/doc22");
$mydoc->SetData("This is document Two 2");
$mydoc->FileExtension = ".html";
$mydoc->AddMetadata("authors", "jdst@coveo.com");
$mydoc->Author = "Wim";
$mydoc->Title = "What's up Doc 22?";


// Define a list of users that should have access to the document.
$users = array("Wim","Peter");

// Define a list of users that should not have access to the document.
$deniedusers = array("Alex","Anne");

// Define a list of groups that should have access to the document.
$groups = array("HR","RD","SALES");

// Create the permission Levels. Each level can include multiple sets.
$permLevel1 = new  DocumentPermissionLevel('First');
$permLevel1Set1 = new DocumentPermissionSet('1Set1');
$permLevel1Set2 = new DocumentPermissionSet('1Set2');
$permLevel1Set1->AllowAnonymous = False;
$permLevel1Set2->AllowAnonymous = False;
$permLevel2 = new DocumentPermissionLevel('Second');
$permLevel2Set = new DocumentPermissionSet('2Set1');
$permLevel2Set->AllowAnonymous = False;

// Set the allowed permissions for the first set of the first level
foreach ($users as $user) {
    // Create the permission identity
    $permLevel1Set1->AddAllowedPermissions(
    new PermissionIdentity($GUSER, $mysecprovidername, $user));
}

// Set the denied permissions for the second set of the first level
foreach ($deniedusers as $user) {
  // Create the permission identity
  $permLevel1Set2->AddDeniedPermissions(
  new  PermissionIdentity($GUSER, $mysecprovidername, $user));
}

// Set the allowed permissions for the first set of the second level
foreach ($groups as $group) {
  // Create the permission identity
  $permLevel2Set->AddAllowedPermissions(
  new PermissionIdentity($GGROUP, $mysecprovidername, $group));
}

// Set the permission sets to the appropriate level
$permLevel1->AddPermissionSet($permLevel1Set1);
$permLevel1->AddPermissionSet($permLevel1Set2);
$permLevel2->AddPermissionSet($permLevel2Set);

// Set the permissions on the document
array_push($mydoc->Permissions, $permLevel1);
array_push($mydoc->Permissions, $permLevel2);

// Push the document
$push->AddSingleDocument($mydoc);

// First do a single call to update an identity
// We now also need to add the expansion/memberships/mappings to the security cache
// The previouslt defined identities were: alex, anne, wim, peter

$usersingroup =array("wimingroup","peteringroup");

// Remove the last group, so we can add it later with a single call
array_pop($groups);

$push->StartExpansion($mysecprovidername);

// group memberships for: HR, RD
foreach ($groups as $group) {
    // for each group set the users
    $members = array();
    foreach ($usersingroup as $user) {
        // Create a permission Identity
        array_push($members,
          new PermissionIdentityExpansion($GUSER, $mysecprovidername, $user));
    }
    $logger->LogWindow($GGROUP);
    $logger->LogWindow($group);
    $push->AddExpansionMember(
      new PermissionIdentityExpansion($GGROUP, $mysecprovidername, $group), $members, array(), array());
}

// mappings for all users, from userid to email address
$users = array_merge($users,$deniedusers);
$users = array_merge($users,$usersingroup);
foreach ($users as $user) {
    // Create a permission Identity
    $mappings = array();
    array_push($mappings,new PermissionIdentityExpansion($GUSER, "Email Security Provider", $user . "@coveo.com"));

    $wellknowns = array();
    array_push($wellknowns, new PermissionIdentityExpansion($GGROUP, $mysecprovidername, "Everyone"));

    $push->AddExpansionMapping(
      new PermissionIdentityExpansion($GUSER, $mysecprovidername, $user), array(), $mappings, $wellknowns);
}

// Remove deleted users
// Deleted Users
$delusers = array("wimn","petern");
foreach ($delusers as $user) {
  // Add each identity to delete to the Deleted
    $push->AddExpansionDeleted(
      new PermissionIdentityExpansion($GUSER, $mysecprovidername, $user),array(),array(),array());
}
// End the expansion and write the last batch
$push->EndExpansion($mysecprovidername);

$logger->LogWindow("Now updating security cache.");
$logger->LogWindow("Check:");
$logger->LogWindow(" HR/RD groups: members wimingroup, peteringroup");
$logger->LogWindow(" SALES: should not have any members");
$logger->LogWindow(" each user: wim, peter, anne, wimingroup should have also mappings to Email security providers");


//sleep(5000);

$logger->LogWindow("Changing security");

// Add a single call, add the Sales group
$usersingroup =array("wiminsalesgroup", "peterinsalesgroup");

$members = array();
foreach ($usersingroup as $user) {
    // Create a permission identity
    $mappings = array(new PermissionIdentityExpansion($GUSER, "Email Security Provider", $user . "@coveo.com"));

    $wellknowns = array(new PermissionIdentityExpansion($GGROUP, $mysecprovidername, "Everyone"));
    array_push($members, new PermissionIdentityExpansion($GUSER, $mysecprovidername, $user));

    $push->AddPermissionExpansion(
        $mysecprovidername,
        new PermissionIdentityExpansion($GUSER, $mysecprovidername, $user),array(), $mappings, $wellknowns);
}

$push->AddPermissionExpansion(
    $mysecprovidername,
    new PermissionIdentityExpansion($GGROUP, $mysecprovidername, "Everyone"), $members, array(),array());

$push->AddPermissionExpansion(
    $mysecprovidername,
    new PermissionIdentityExpansion($GGROUP, $mysecprovidername, "SALES"), $members, array(),array());


$logger->LogWindow( "Now updating security cache.");
$logger->LogWindow("Check:");
$logger->LogWindow(" HR/RD groups: members wimingroup, peteringroup");
$logger->LogWindow(" SALES: should have members wiminsalesgroup, peterinsalesgroup");
$logger->LogWindow(" each user: wim, peter, anne, wimingroup should also have mappings to Email security providers");
//sleep(5000);

// Remove a Identity
// Group SALES should be removed
$push->RemovePermissionIdentity($mysecprovidername, new PermissionIdentityExpansion($GGROUP, $mysecprovidername, "SALES"));

$logger->LogWindow("Now updating security cache.");
$logger->LogWindow("Check:");
$logger->LogWindow(" HR/RD groups: members wimingroup,peteringroup");
$logger->LogWindow(" NO wiminsalesgroup,peterinsalesgroup");
$logger->LogWindow(" each user: wim, peter, anne, wimingroup should have also mappings to Email security providers");


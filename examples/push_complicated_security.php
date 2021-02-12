<?php
// -------------------------------------------------------------------------------------
// Push single document with complicated security
// -------------------------------------------------------------------------------------

require_once('../coveopush/CoveoConstants.php');
require_once('../coveopush/CoveoDocument.php');
require_once('../coveopush/CoveoPermissions.php');
require_once('../coveopush/CoveoPush.php');
require_once('../coveopush/Enum.php');

function log2($text){
  echo "<BR>";
  echo $text;
}

require_once('config.php');

// Shortcut for constants
$GGROUP = Coveo\SDKPushPHP\PermissionIdentityType::Group;
$GUSER = Coveo\SDKPushPHP\PermissionIdentityType::User;

log2('Make sure that your API KEY has the rights to modify Security Providers !!!!');
log2('Make sure that your Push Source has Security: Determined by source permissions set');
// Setup the push client
$push = new Coveo\SDKPushPHP\Push($sourceId, $orgId, $apiKey);

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
log2("Old ids removed. Updating security cache");
//sleep(25);

// Create a document
$mydoc = new Coveo\SDKPushPHP\Document("https://myreference.cov.com/doc22");
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
$permLevel1 = new Coveo\SDKPushPHP\DocumentPermissionLevel('First');
$permLevel1Set1 = new Coveo\SDKPushPHP\DocumentPermissionSet('1Set1');
$permLevel1Set2 = new Coveo\SDKPushPHP\DocumentPermissionSet('1Set2');
$permLevel1Set1->AllowAnonymous = False;
$permLevel1Set2->AllowAnonymous = False;
$permLevel2 = new Coveo\SDKPushPHP\DocumentPermissionLevel('Second');
$permLevel2Set = new Coveo\SDKPushPHP\DocumentPermissionSet('2Set1');
$permLevel2Set->AllowAnonymous = False;

// Set the allowed permissions for the first set of the first level
foreach ($users as $user) {
    // Create the permission identity
    $permLevel1Set1->AddAllowedPermissions(
    new Coveo\SDKPushPHP\PermissionIdentity($GUSER, $mysecprovidername, $user));
}

// Set the denied permissions for the second set of the first level
foreach ($deniedusers as $user) {
  // Create the permission identity
  $permLevel1Set2->AddDeniedPermissions(
  new Coveo\SDKPushPHP\PermissionIdentity($GUSER, $mysecprovidername, $user));
}

// Set the allowed permissions for the first set of the second level
foreach ($groups as $group) {
  // Create the permission identity
  $permLevel2Set->AddAllowedPermissions(
  new Coveo\SDKPushPHP\PermissionIdentity($GGROUP, $mysecprovidername, $group));
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
          new Coveo\SDKPushPHP\PermissionIdentityExpansion($GUSER, $mysecprovidername, $user));
    }
    log2($GGROUP);
    log2($group);
    $push->AddExpansionMember(
      new Coveo\SDKPushPHP\PermissionIdentityExpansion($GGROUP, $mysecprovidername, $group), $members, array(), array());
}

// mappings for all users, from userid to email address
$users = array_merge($users,$deniedusers);
$users = array_merge($users,$usersingroup);
foreach ($users as $user) {
    // Create a permission Identity
    $mappings = array();
    array_push($mappings,new Coveo\SDKPushPHP\PermissionIdentityExpansion($GUSER, "Email Security Provider", $user . "@coveo.com"));

    $wellknowns = array();
    array_push($wellknowns, new Coveo\SDKPushPHP\PermissionIdentityExpansion($GGROUP, $mysecprovidername, "Everyone"));

    $push->AddExpansionMapping(
      new Coveo\SDKPushPHP\PermissionIdentityExpansion($GUSER, $mysecprovidername, $user), array(), $mappings, $wellknowns);
}

// Remove deleted users
// Deleted Users
$delusers = array("wimn","petern");
foreach ($delusers as $user) {
  // Add each identity to delete to the Deleted
    $push->AddExpansionDeleted(
      new Coveo\SDKPushPHP\PermissionIdentityExpansion($GUSER, $mysecprovidername, $user),array(),array(),array());
}
// End the expansion and write the last batch
$push->EndExpansion($mysecprovidername);

log2("Now updating security cache.");
log2("Check:");
log2(" HR/RD groups: members wimingroup, peteringroup");
log2(" SALES: should not have any members");
log2(" each user: wim, peter, anne, wimingroup should have also mappings to Email security providers");


//sleep(5000);

log2("Changing security");

// Add a single call, add the Sales group
$usersingroup =array("wiminsalesgroup", "peterinsalesgroup");

$members = array();
foreach ($usersingroup as $user) {
    // Create a permission identity
    $mappings = array(new Coveo\SDKPushPHP\PermissionIdentityExpansion($GUSER, "Email Security Provider", $user . "@coveo.com"));

    $wellknowns = array(new Coveo\SDKPushPHP\PermissionIdentityExpansion($GGROUP, $mysecprovidername, "Everyone"));
    array_push($members, new Coveo\SDKPushPHP\PermissionIdentityExpansion($GUSER, $mysecprovidername, $user));

    $push->AddPermissionExpansion(
        $mysecprovidername,
        new Coveo\SDKPushPHP\PermissionIdentityExpansion($GUSER, $mysecprovidername, $user),array(), $mappings, $wellknowns);
}

$push->AddPermissionExpansion(
    $mysecprovidername,
    new Coveo\SDKPushPHP\PermissionIdentityExpansion($GGROUP, $mysecprovidername, "Everyone"), $members, array(),array());

$push->AddPermissionExpansion(
    $mysecprovidername,
    new Coveo\SDKPushPHP\PermissionIdentityExpansion($GGROUP, $mysecprovidername, "SALES"), $members, array(),array());


log2( "Now updating security cache.");
log2("Check:");
log2(" HR/RD groups: members wimingroup, peteringroup");
log2(" SALES: should have members wiminsalesgroup, peterinsalesgroup");
log2(" each user: wim, peter, anne, wimingroup should also have mappings to Email security providers");
//sleep(5000);

// Remove a Identity
// Group SALES should be removed
$push->RemovePermissionIdentity($mysecprovidername, new Coveo\SDKPushPHP\PermissionIdentityExpansion($GGROUP, $mysecprovidername, "SALES"));

log2("Now updating security cache.");
log2("Check:");
log2(" HR/RD groups: members wimingroup,peteringroup");
log2(" NO wiminsalesgroup,peterinsalesgroup");
log2(" each user: wim, peter, anne, wimingroup should have also mappings to Email security providers");


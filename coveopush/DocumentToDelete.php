<?php
// -------------------------------------------------------------------------------------
// CoveoDocument
// -------------------------------------------------------------------------------------
// Contains the CoveoDocument class
//   A CoveoDocument will be pushed to the push source
// -------------------------------------------------------------------------------------
namespace Coveo\Search\SDK\SDKPushPHP;
/**
 * Class DocumentToDelete.
 * Class to hold the Document To Delete.
 * It should consist of the DocumentId (URL) only.
 */
class DocumentToDelete{

    // The unique document identifier for the source, must be the document URI.
    public $DocumentId = '';
    public $Title = '';

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function __construct(string $p_DocumentId) {
        $this->DocumentId = $p_DocumentId;
        $this->Title = $p_DocumentId;
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function ToJson(){
        /*"""ToJson, returns JSON for push.
        Puts all metadata and other fields into clean"""*/
        // Check if empty

        $all=array();
        $all["DocumentId"] = $this->DocumentId;
        return json_encode($all);
    }
}

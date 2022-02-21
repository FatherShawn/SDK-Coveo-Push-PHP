<?php


namespace Coveo\Search\SDK\SDKPushPHP;

/**
 * Class to hold the Document To Delete. It should consist of the DocumentId (URL) only. A CoveoDocument will be pushed to the push source
 */
class DocumentToDelete {
  /**
   * The unique document identifier for the source, must be the document URI.
   *
   * @var string
   */
  public $DocumentId = '';

  /**
   * Document Title.
   *
   * @var string
   */
  public $Title = '';

  /**
   * Document Title.
   *
   * @var bool
   */
  public $deleteChildren = FALSE;

  /**
   * Constructor.
   *
   * @param string $p_DocumentId
   *   The document Id.
   */
  function __construct(string $p_DocumentId, bool $p_deleteChildren = NULL ) {
    $this->DocumentId = $p_DocumentId;
    $this->Title = $p_DocumentId;
    $delete = $p_deleteChildren ?? FALSE;
    $this->deleteChildren = $delete;

  }

  /**
   * Puts all metadata and other fields into clean.
   */
  function ToJson() {
    // Check if empty
    $all = array();
    $all["DocumentId"] = $this->DocumentId;
    $all["deleteChildren"] = $this->deleteChildren;
    return json_encode($all);
  }

    /**
   * Validates if all properties on the Coveo Document are properly set.
   *
   * @return bool|Exception
   */
  function Validate() {
    $result = TRUE;
    $error = array();

    return array($result, implode(' | ', $error));
  }

  
  /**
   * Puts all metadata and other fields into a clean JSON object.
   *
   * @return string|bool
   *   A JSON encoded string on success or FALSE on failure.
   */
  function cleanUp() {
    $source = $this->ToJson();
    //echo json_encode($all);
    //Debug($source);
    $result = preg_replace('/,\s*"[^"]+":null|"[^"]+":null,?/', '', $source);
    $result = preg_replace('/,\s*"[^"]+":\[\]|"[^"]+":\[\],?/', '', $source);
    //Debug($result);
    //return $result;
    return json_decode($result);
  }
}

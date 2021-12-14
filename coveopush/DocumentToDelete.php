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
   * Constructor.
   *
   * @param string $p_DocumentId
   *   The document Id.
   */
  function __construct(string $p_DocumentId) {
    $this->DocumentId = $p_DocumentId;
    $this->Title = $p_DocumentId;
  }

  /**
   * Puts all metadata and other fields into clean.
   */
  function ToJson() {
    // Check if empty
    $all = array();
    $all["DocumentId"] = $this->DocumentId;
    return json_encode($all);
  }

}

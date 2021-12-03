<?php

namespace Coveo\Search\SDK\SDKPushPHP;

/**
 * Constants used within the Push Classes. The default request timeout in seconds.
 */
class Constants {
  const DEFAULT_REQUEST_TIMEOUT_IN_SECONDS = 100;

  // The default date format used by the Push API.
  const DATE_FORMAT_STRING = "yyyy-MM-dd HH:mm:ss";

  // The date format used by the Activities service of the Platform API.
  const DATE_WITH_MILLISECONDS_FORMAT_STRING = "yyyy-MM-ddTHH:mm:ss.fffZ";

  // The name of the default 'Email' security provider provisioned with each organization.
  const EMAIL_SECURITY_PROVIDER_NAME = "Email Security Provider";

  // Max size (in bytes) of a document after being compressed-encoded.
  const COMPRESSED_DATA_MAX_SIZE_IN_BYTES = 5*1024*1024;

  // Max size (in bytes) of a request.
  // Limit in the Push API consumer is 256MB. --> was to big, we use 250 to be safe
  // 32 bytes is removed from it to account for the JSON body structure.
  const MAXIMUM_REQUEST_SIZE_IN_BYTES = 250*1024*1024;

    // Reserved key names (case-insensitive) used in the Push API.
    const s_DocumentReservedKeys = array("author","clickableUri", "compressedBinaryData","compressedBinaryDataFileId","compressionType","data","date","documentId", "fileExtension", "parentId", "permissions", "orderingId" );
}

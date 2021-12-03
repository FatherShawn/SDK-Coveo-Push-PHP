<?php

namespace Coveo\Search\SDK\SDKPushPHP;

use Coveo\Search\SDK\SDKPushPHP\Enum as Enum;

/**
 * Contains the CompressionType used by the SDK.
 */
class CompressionType extends Enum {
  const UNCOMPRESSED = "UNCOMPRESSED";
  const DEFLATE = "DEFLATE";
  const GZIP = "GZIP";
  const LZMA = "LZMA";
  const ZLIB = "ZLIB";

}

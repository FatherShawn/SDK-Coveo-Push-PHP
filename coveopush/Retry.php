<?php
// -------------------------------------------------------------------------------------
// CompressionType
// -------------------------------------------------------------------------------------
// Contains the CompressionType used by the SDK
// -------------------------------------------------------------------------------------

namespace Coveo\Search\SDK\SDKPushPHP;

class Retry {
    // The default number of retries when a request fails on a retryable error.
    const DEFAULT_NUMBER_OF_RETRIES = 5;

    // The default initial waiting time in milliseconds when a retry is performed.
    const DEFAULT_INITIAL_WAITING_TIME_IN_MS = 2000;

    // The maximum waiting time interval in milliseconds to add for each retry.
    const DEFAULT_MAX_INTERVAL_TIME_TO_ADD_IN_MS = 2000;
}

<?php

// File generated from our OpenAPI spec

namespace StellarWP\Learndash\Stripe;

/**
 * @property string $id Unique identifier for the object.
 * @property string $object String representing the object's type. Objects of the same type share the same value.
 * @property int $created Time at which the object was created. Measured in seconds since the Unix epoch.
 * @property string $domain_name
 * @property bool $livemode Has the value <code>true</code> if the object exists in live mode or the value <code>false</code> if the object exists in test mode.
 *
 * @license MIT
 * Modified by learndash on 05-January-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */
class ApplePayDomain extends ApiResource
{
    const OBJECT_NAME = 'apple_pay_domain';

    use ApiOperations\All;
    use ApiOperations\Create;
    use ApiOperations\Delete;
    use ApiOperations\Retrieve;

    /**
     * @return string The class URL for this resource. It needs to be special
     *    cased because it doesn't fit into the standard resource pattern.
     */
    public static function classUrl()
    {
        return '/v1/apple_pay/domains';
    }
}

<?php

// File generated from our OpenAPI spec

namespace StellarWP\Learndash\Stripe;

/**
 * @property string $id Unique identifier for the object.
 * @property string $object String representing the object's type. Objects of the same type share the same value.
 * @property null|string $invoice The invoice in which this usage period has been billed for.
 * @property bool $livemode Has the value <code>true</code> if the object exists in live mode or the value <code>false</code> if the object exists in test mode.
 * @property \StellarWP\Learndash\Stripe\StripeObject $period
 * @property string $subscription_item The ID of the subscription item this summary is describing.
 * @property int $total_usage The total usage within this usage period.
 *
 * @license MIT
 * Modified by learndash on 05-January-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */
class UsageRecordSummary extends ApiResource
{
    const OBJECT_NAME = 'usage_record_summary';
}

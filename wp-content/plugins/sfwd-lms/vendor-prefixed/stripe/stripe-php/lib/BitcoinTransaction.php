<?php

// File generated from our OpenAPI spec

namespace StellarWP\Learndash\Stripe;

/**
 * @property string $id Unique identifier for the object.
 * @property string $object String representing the object's type. Objects of the same type share the same value.
 * @property int $amount The amount of <code>currency</code> that the transaction was converted to in real-time.
 * @property int $bitcoin_amount The amount of bitcoin contained in the transaction.
 * @property int $created Time at which the object was created. Measured in seconds since the Unix epoch.
 * @property string $currency Three-letter <a href="https://stripe.com/docs/currencies">ISO code for the currency</a> to which this transaction was converted.
 * @property string $receiver The receiver to which this transaction was sent.
 *
 * @license MIT
 * Modified by learndash on 05-January-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */
class BitcoinTransaction extends ApiResource
{
    const OBJECT_NAME = 'bitcoin_transaction';
}

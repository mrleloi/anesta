<?php

// File generated from our OpenAPI spec

namespace StellarWP\Learndash\Stripe;

/**
 * A line item.
 *
 * @property string $id Unique identifier for the object.
 * @property string $object String representing the object's type. Objects of the same type share the same value.
 * @property int $amount_subtotal Total before any discounts or taxes are applied.
 * @property int $amount_total Total after discounts and taxes.
 * @property string $currency Three-letter <a href="https://www.iso.org/iso-4217-currency-codes.html">ISO currency code</a>, in lowercase. Must be a <a href="https://stripe.com/docs/currencies">supported currency</a>.
 * @property string $description An arbitrary string attached to the object. Often useful for displaying to users. Defaults to product name.
 * @property \StellarWP\Learndash\Stripe\StripeObject[] $discounts The discounts applied to the line item.
 * @property null|\StellarWP\Learndash\Stripe\Price $price The price used to generate the line item.
 * @property null|int $quantity The quantity of products being purchased.
 * @property \StellarWP\Learndash\Stripe\StripeObject[] $taxes The taxes applied to the line item.
 *
 * @license MIT
 * Modified by learndash on 05-January-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */
class LineItem extends ApiResource
{
    const OBJECT_NAME = 'item';

    use ApiOperations\All;
}

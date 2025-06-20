<?php

// File generated from our OpenAPI spec

namespace StellarWP\Learndash\Stripe;

/**
 * @property string $id Unique identifier for the object.
 * @property string $object String representing the object's type. Objects of the same type share the same value.
 * @property int $amount The integer amount in %s representing the gross amount being credited for this line item, excluding (exclusive) tax and discounts.
 * @property null|string $description Description of the item being credited.
 * @property int $discount_amount The integer amount in %s representing the discount being credited for this line item.
 * @property \StellarWP\Learndash\Stripe\StripeObject[] $discount_amounts The amount of discount calculated per discount for this line item
 * @property string $invoice_line_item ID of the invoice line item being credited
 * @property bool $livemode Has the value <code>true</code> if the object exists in live mode or the value <code>false</code> if the object exists in test mode.
 * @property null|int $quantity The number of units of product being credited.
 * @property \StellarWP\Learndash\Stripe\StripeObject[] $tax_amounts The amount of tax calculated per tax rate for this line item
 * @property \StellarWP\Learndash\Stripe\TaxRate[] $tax_rates The tax rates which apply to the line item.
 * @property string $type The type of the credit note line item, one of <code>invoice_line_item</code> or <code>custom_line_item</code>. When the type is <code>invoice_line_item</code> there is an additional <code>invoice_line_item</code> property on the resource the value of which is the id of the credited line item on the invoice.
 * @property null|int $unit_amount The cost of each unit of product being credited.
 * @property null|string $unit_amount_decimal Same as <code>unit_amount</code>, but contains a decimal value with at most 12 decimal places.
 *
 * @license MIT
 * Modified by learndash on 05-January-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */
class CreditNoteLineItem extends ApiResource
{
    const OBJECT_NAME = 'credit_note_line_item';
}

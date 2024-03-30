<?php

// File generated from our OpenAPI spec

namespace StellarWP\Learndash\Stripe;

/**
 * A SetupAttempt describes one attempted confirmation of a SetupIntent, whether
 * that confirmation was successful or unsuccessful. You can use SetupAttempts to
 * inspect details of a specific attempt at setting up a payment method using a
 * SetupIntent.
 *
 * @property string $id Unique identifier for the object.
 * @property string $object String representing the object's type. Objects of the same type share the same value.
 * @property null|string|\StellarWP\Learndash\Stripe\StripeObject $application The value of <a href="https://stripe.com/docs/api/setup_intents/object#setup_intent_object-application">application</a> on the SetupIntent at the time of this confirmation.
 * @property int $created Time at which the object was created. Measured in seconds since the Unix epoch.
 * @property null|string|\StellarWP\Learndash\Stripe\Customer $customer The value of <a href="https://stripe.com/docs/api/setup_intents/object#setup_intent_object-customer">customer</a> on the SetupIntent at the time of this confirmation.
 * @property bool $livemode Has the value <code>true</code> if the object exists in live mode or the value <code>false</code> if the object exists in test mode.
 * @property null|string|\StellarWP\Learndash\Stripe\Account $on_behalf_of The value of <a href="https://stripe.com/docs/api/setup_intents/object#setup_intent_object-on_behalf_of">on_behalf_of</a> on the SetupIntent at the time of this confirmation.
 * @property string|\StellarWP\Learndash\Stripe\PaymentMethod $payment_method ID of the payment method used with this SetupAttempt.
 * @property \StellarWP\Learndash\Stripe\StripeObject $payment_method_details
 * @property null|\StellarWP\Learndash\Stripe\ErrorObject $setup_error The error encountered during this attempt to confirm the SetupIntent, if any.
 * @property string|\StellarWP\Learndash\Stripe\SetupIntent $setup_intent ID of the SetupIntent that this attempt belongs to.
 * @property string $status Status of this SetupAttempt, one of <code>requires_confirmation</code>, <code>requires_action</code>, <code>processing</code>, <code>succeeded</code>, <code>failed</code>, or <code>abandoned</code>.
 * @property string $usage The value of <a href="https://stripe.com/docs/api/setup_intents/object#setup_intent_object-usage">usage</a> on the SetupIntent at the time of this confirmation, one of <code>off_session</code> or <code>on_session</code>.
 *
 * @license MIT
 * Modified by learndash on 05-January-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */
class SetupAttempt extends ApiResource
{
    const OBJECT_NAME = 'setup_attempt';

    use ApiOperations\All;
}

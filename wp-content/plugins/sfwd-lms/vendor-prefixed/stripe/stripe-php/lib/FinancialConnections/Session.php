<?php

// File generated from our OpenAPI spec

namespace StellarWP\Learndash\Stripe\FinancialConnections;

/**
 * A Financial Connections Session is the secure way to programmatically launch the
 * client-side Stripe.js modal that lets your users link their accounts.
 *
 * @property string $id Unique identifier for the object.
 * @property string $object String representing the object's type. Objects of the same type share the same value.
 * @property null|\StellarWP\Learndash\Stripe\StripeObject $account_holder The account holder for whom accounts are collected in this session.
 * @property \StellarWP\Learndash\Stripe\Collection<\Stripe\FinancialConnections\Account> $accounts The accounts that were collected as part of this Session.
 * @property string $client_secret A value that will be passed to the client to launch the authentication flow.
 * @property \StellarWP\Learndash\Stripe\StripeObject $filters
 * @property bool $livemode Has the value <code>true</code> if the object exists in live mode or the value <code>false</code> if the object exists in test mode.
 * @property string[] $permissions Permissions requested for accounts collected during this session.
 * @property string $return_url For webview integrations only. Upon completing OAuth login in the native browser, the user will be redirected to this URL to return to your app.
 *
 * @license MIT
 * Modified by learndash on 05-January-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */
class Session extends \StellarWP\Learndash\Stripe\ApiResource
{
    const OBJECT_NAME = 'financial_connections.session';

    use \StellarWP\Learndash\Stripe\ApiOperations\Create;
    use \StellarWP\Learndash\Stripe\ApiOperations\Retrieve;
}

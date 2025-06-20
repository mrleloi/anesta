<?php

// File generated from our OpenAPI spec

namespace StellarWP\Learndash\Stripe\Radar;

/**
 * Value list items allow you to add specific values to a given Radar value list,
 * which can then be used in rules.
 *
 * Related guide: <a
 * href="https://stripe.com/docs/radar/lists#managing-list-items">Managing List
 * Items</a>.
 *
 * @property string $id Unique identifier for the object.
 * @property string $object String representing the object's type. Objects of the same type share the same value.
 * @property int $created Time at which the object was created. Measured in seconds since the Unix epoch.
 * @property string $created_by The name or email address of the user who added this item to the value list.
 * @property bool $livemode Has the value <code>true</code> if the object exists in live mode or the value <code>false</code> if the object exists in test mode.
 * @property string $value The value of the item.
 * @property string $value_list The identifier of the value list this item belongs to.
 *
 * @license MIT
 * Modified by learndash on 05-January-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */
class ValueListItem extends \StellarWP\Learndash\Stripe\ApiResource
{
    const OBJECT_NAME = 'radar.value_list_item';

    use \StellarWP\Learndash\Stripe\ApiOperations\All;
    use \StellarWP\Learndash\Stripe\ApiOperations\Create;
    use \StellarWP\Learndash\Stripe\ApiOperations\Delete;
    use \StellarWP\Learndash\Stripe\ApiOperations\Retrieve;
}

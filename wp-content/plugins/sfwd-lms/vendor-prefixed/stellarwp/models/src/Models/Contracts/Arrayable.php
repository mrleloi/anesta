<?php
/**
 * @license GPL-3.0-or-later
 *
 * Modified by learndash on 05-January-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace StellarWP\Learndash\StellarWP\Models\Contracts;

/**
 * @since 1.0.0
 */
interface Arrayable {
	/**
	 * Get the instance as an array.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function toArray() : array;
}

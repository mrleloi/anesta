<?php
/**
 * @license GPL-3.0-or-later
 *
 * Modified by learndash on 05-January-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace StellarWP\Learndash\StellarWP\Models\Repositories\Contracts;

use StellarWP\Learndash\StellarWP\Models\Contracts\Model;

interface Deletable {
	/**
	 * Inserts a model record.
	 *
	 * @since 1.0.0
	 *
	 * @param Model $model
	 *
	 * @return bool
	 */
	public function delete( Model $model ) : bool;
}

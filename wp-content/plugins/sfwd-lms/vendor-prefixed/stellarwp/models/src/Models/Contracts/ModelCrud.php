<?php
/**
 * @license GPL-3.0-or-later
 *
 * Modified by learndash on 05-January-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace StellarWP\Learndash\StellarWP\Models\Contracts;

use StellarWP\Learndash\StellarWP\Models\ModelQueryBuilder;

/**
 * @since 1.0.0
 */
interface ModelCrud {
	/**
	 * @since 1.0.0
	 *
	 * @param int $id
	 *
	 * @return Model
	 */
	public static function find( $id );

	/**
	 * @since 1.0.0
	 *
	 * @param array $attributes
	 *
	 * @return Model
	 */
	public static function create( array $attributes );

	/**
	 * @since 1.0.0
	 *
	 * @return Model
	 */
	public function save();

	/**
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function delete() : bool;

	/**
	 * @since 1.0.0
	 *
	 * @return ModelQueryBuilder
	 */
	public static function query();
}

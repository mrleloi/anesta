<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by learndash on 05-January-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace StellarWP\Learndash\StellarWP\Validation\Rules;

use Closure;
use StellarWP\Learndash\StellarWP\Validation\Contracts\Sanitizer;
use StellarWP\Learndash\StellarWP\Validation\Contracts\ValidatesOnFrontEnd;
use StellarWP\Learndash\StellarWP\Validation\Contracts\ValidationRule;

use const FILTER_NULL_ON_FAILURE;

class Boolean implements ValidationRule, ValidatesOnFrontEnd, Sanitizer
{
    /**
     * {@inheritDoc}
     *
     * @since 1.4.0
     */
    public static function id(): string
    {
        return 'boolean';
    }

    /**
     * {@inheritDoc}
     *
     * @since 1.4.0
     */
    public static function fromString(string $options = null): ValidationRule
    {
        return new self();
    }

    /**
     * {@inheritDoc}
     *
     * @since 1.4.1 add is_bool check and FILTER_NULL_ON_FAILURE flag to prevent false positives
     * @since 1.4.0
     */
    public function __invoke($value, Closure $fail, string $key, array $values)
    {
        if (!is_bool(filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE))) {
            $fail(sprintf(__('%s must be a boolean', '%TEXTDOMAIN%'), '{field}'));
        }
    }

    /**
     * {@inheritDoc}
     *
     * @since 1.4.0
     */
    public function serializeOption()
    {
        return null;
    }

    /**
     * {@inheritDoc}
     *
     * @since 1.4.0
     */
    public function sanitize($value)
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}

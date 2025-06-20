<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by learndash on 05-January-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace StellarWP\Learndash\StellarWP\Validation\Rules;

use Closure;

class InStrict extends In
{
    /**
     * @since 1.2.0
     */
    public static function id(): string
    {
        return 'inStrict';
    }

    /**
     * @since 1.2.0
     */
    public function __invoke($value, Closure $fail, string $key, array $values)
    {
        if (!in_array($value, $this->acceptedValues, true)) {
            $fail(sprintf(__('%s must be one of %s', '%TEXTDOMAIN%'), '{field}', implode(', ', $this->acceptedValues)));
        }
    }
}

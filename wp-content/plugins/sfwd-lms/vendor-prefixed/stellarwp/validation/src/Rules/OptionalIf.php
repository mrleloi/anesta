<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by learndash on 05-January-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

declare(strict_types=1);

namespace StellarWP\Learndash\StellarWP\Validation\Rules;

use Closure;
use StellarWP\Learndash\StellarWP\Validation\Commands\SkipValidationRules;
use StellarWP\Learndash\StellarWP\Validation\Rules\Abstracts\ConditionalRule;

/**
 * Mark the value as optional if the conditions pass
 *
 * @since 1.2.0
 *
 * @see Optional
 */
class OptionalIf extends ConditionalRule
{
    /**
     * @since 1.2.0
     */
    public static function id(): string
    {
        return 'optionalIf';
    }

    /**
     * @since 1.2.0
     */
    public function __invoke($value, Closure $fail, string $key, array $values)
    {
        if (($value === '' || $value === null) && $this->conditions->passes($values)) {
            return new SkipValidationRules();
        }
    }
}

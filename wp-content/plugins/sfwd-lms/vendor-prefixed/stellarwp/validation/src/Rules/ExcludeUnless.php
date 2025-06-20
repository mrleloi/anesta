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
use StellarWP\Learndash\StellarWP\Validation\Commands\ExcludeValue;
use StellarWP\Learndash\StellarWP\Validation\Rules\Abstracts\ConditionalRule;

/**
 * Exclude a field unless the given conditions are met.
 *
 * @see Exclude
 *
 * @since 1.2.0
 */
class ExcludeUnless extends ConditionalRule
{
    /**
     * @inheritDoc
     *
     * @since 1.2.0
     */
    public static function id(): string
    {
        return 'excludeUnless';
    }

    /**
     * @inheritDoc
     *
     * @since 1.2.0
     */
    public function __invoke($value, Closure $fail, string $key, array $values)
    {
        if ($this->conditions->fails($values)) {
            return new ExcludeValue();
        }
    }
}

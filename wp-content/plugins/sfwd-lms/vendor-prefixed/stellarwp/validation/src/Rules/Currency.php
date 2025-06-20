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
use StellarWP\Learndash\StellarWP\Validation\Contracts\ValidatesOnFrontEnd;
use StellarWP\Learndash\StellarWP\Validation\Contracts\ValidationRule;

class Currency implements ValidationRule, ValidatesOnFrontEnd
{
    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public static function id(): string
    {
        return 'currency';
    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public static function fromString(string $options = null): ValidationRule
    {
        return new self();
    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function serializeOption()
    {
        return null;
    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function __invoke($value, Closure $fail, string $key, array $values)
    {
        if (!is_string($value) || !in_array(strtoupper($value), self::currencyCodes(), true)) {
            $fail(sprintf(__('%s must be a valid currency', '%TEXTDOMAIN%'), '{field}'));
        }
    }

    /**
     * @since 1.0.0
     *
     * @return string[]
     */
    public static function currencyCodes(): array
    {
        static $codes = null;

        if ($codes === null) {
            $codes = [
                "ALL",
                "AFN",
                "ARS",
                "AWG",
                "AUD",
                "AZN",
                "BSD",
                "BBD",
                "BDT",
                "BYR",
                "BZD",
                "BMD",
                "BOB",
                "BAM",
                "BWP",
                "BGN",
                "BRL",
                "BND",
                "KHR",
                "CAD",
                "KYD",
                "CLP",
                "CNY",
                "COP",
                "CRC",
                "HRK",
                "CUP",
                "CZK",
                "DKK",
                "DOP",
                "XCD",
                "EGP",
                "SVC",
                "EEK",
                "EUR",
                "FKP",
                "FJD",
                "GHC",
                "GIP",
                "GTQ",
                "GGP",
                "GYD",
                "HNL",
                "HKD",
                "HUF",
                "ISK",
                "INR",
                "IDR",
                "IRR",
                "IMP",
                "ILS",
                "JMD",
                "JPY",
                "JEP",
                "KZT",
                "KPW",
                "KRW",
                "KGS",
                "LAK",
                "LVL",
                "LBP",
                "LRD",
                "LTL",
                "MKD",
                "MYR",
                "MUR",
                "MXN",
                "MNT",
                "MZN",
                "NAD",
                "NPR",
                "ANG",
                "NZD",
                "NIO",
                "NGN",
                "NOK",
                "OMR",
                "PKR",
                "PAB",
                "PYG",
                "PEN",
                "PHP",
                "PLN",
                "QAR",
                "RON",
                "RUB",
                "SHP",
                "SAR",
                "RSD",
                "SCR",
                "SGD",
                "SBD",
                "SOS",
                "ZAR",
                "LKR",
                "SEK",
                "CHF",
                "SRD",
                "SYP",
                "TWD",
                "THB",
                "TTD",
                "TRY",
                "TRL",
                "TVD",
                "UAH",
                "GBP",
                "USD",
                "UYU",
                "UZS",
                "VEF",
                "VND",
                "YER",
                "ZWD",
            ];
        }

        return $codes;
    }
}

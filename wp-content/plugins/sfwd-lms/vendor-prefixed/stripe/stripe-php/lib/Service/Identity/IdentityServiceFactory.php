<?php

// File generated from our OpenAPI spec

namespace StellarWP\Learndash\Stripe\Service\Identity;

/**
 * Service factory class for API resources in the Identity namespace.
 *
 * @property VerificationReportService $verificationReports
 * @property VerificationSessionService $verificationSessions
 *
 * @license MIT
 * Modified by learndash on 05-January-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */
class IdentityServiceFactory extends \StellarWP\Learndash\Stripe\Service\AbstractServiceFactory
{
    /**
     * @var array<string, string>
     */
    private static $classMap = [
        'verificationReports' => VerificationReportService::class,
        'verificationSessions' => VerificationSessionService::class,
    ];

    protected function getServiceClass($name)
    {
        return \array_key_exists($name, self::$classMap) ? self::$classMap[$name] : null;
    }
}

<?php

// File generated from our OpenAPI spec

namespace StellarWP\Learndash\Stripe\Service;

class RefundService extends \StellarWP\Learndash\Stripe\Service\AbstractService
{
    /**
     * Returns a list of all refunds you’ve previously created. The refunds are
     * returned in sorted order, with the most recent refunds appearing first. For
     * convenience, the 10 most recent refunds are always available by default on the
     * charge object.
     *
     * @param null|array $params
     * @param null|array|\StellarWP\Learndash\Stripe\Util\RequestOptions $opts
     *
     * @throws \StellarWP\Learndash\Stripe\Exception\ApiErrorException if the request fails
     *
     * @return \StellarWP\Learndash\Stripe\Collection<\Stripe\Refund>
     *
     * @license MIT
     * Modified by learndash on 05-January-2024 using Strauss.
     * @see https://github.com/BrianHenryIE/strauss
     */
    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/v1/refunds', $params, $opts);
    }

    /**
     * Cancels a refund with a status of <code>requires_action</code>.
     *
     * Refunds in other states cannot be canceled, and only refunds for payment methods
     * that require customer action will enter the <code>requires_action</code> state.
     *
     * @param string $id
     * @param null|array $params
     * @param null|array|\StellarWP\Learndash\Stripe\Util\RequestOptions $opts
     *
     * @throws \StellarWP\Learndash\Stripe\Exception\ApiErrorException if the request fails
     *
     * @return \StellarWP\Learndash\Stripe\Refund
     */
    public function cancel($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/refunds/%s/cancel', $id), $params, $opts);
    }

    /**
     * Create a refund.
     *
     * @param null|array $params
     * @param null|array|\StellarWP\Learndash\Stripe\Util\RequestOptions $opts
     *
     * @throws \StellarWP\Learndash\Stripe\Exception\ApiErrorException if the request fails
     *
     * @return \StellarWP\Learndash\Stripe\Refund
     */
    public function create($params = null, $opts = null)
    {
        return $this->request('post', '/v1/refunds', $params, $opts);
    }

    /**
     * Retrieves the details of an existing refund.
     *
     * @param string $id
     * @param null|array $params
     * @param null|array|\StellarWP\Learndash\Stripe\Util\RequestOptions $opts
     *
     * @throws \StellarWP\Learndash\Stripe\Exception\ApiErrorException if the request fails
     *
     * @return \StellarWP\Learndash\Stripe\Refund
     */
    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/refunds/%s', $id), $params, $opts);
    }

    /**
     * Updates the specified refund by setting the values of the parameters passed. Any
     * parameters not provided will be left unchanged.
     *
     * This request only accepts <code>metadata</code> as an argument.
     *
     * @param string $id
     * @param null|array $params
     * @param null|array|\StellarWP\Learndash\Stripe\Util\RequestOptions $opts
     *
     * @throws \StellarWP\Learndash\Stripe\Exception\ApiErrorException if the request fails
     *
     * @return \StellarWP\Learndash\Stripe\Refund
     */
    public function update($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/refunds/%s', $id), $params, $opts);
    }
}

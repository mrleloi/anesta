<?php

// File generated from our OpenAPI spec

namespace StellarWP\Learndash\Stripe\Service;

class ReviewService extends \StellarWP\Learndash\Stripe\Service\AbstractService
{
    /**
     * Returns a list of <code>Review</code> objects that have <code>open</code> set to
     * <code>true</code>. The objects are sorted in descending order by creation date,
     * with the most recently created object appearing first.
     *
     * @param null|array $params
     * @param null|array|\StellarWP\Learndash\Stripe\Util\RequestOptions $opts
     *
     * @throws \StellarWP\Learndash\Stripe\Exception\ApiErrorException if the request fails
     *
     * @return \StellarWP\Learndash\Stripe\Collection<\Stripe\Review>
     *
     * @license MIT
     * Modified by learndash on 05-January-2024 using Strauss.
     * @see https://github.com/BrianHenryIE/strauss
     */
    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/v1/reviews', $params, $opts);
    }

    /**
     * Approves a <code>Review</code> object, closing it and removing it from the list
     * of reviews.
     *
     * @param string $id
     * @param null|array $params
     * @param null|array|\StellarWP\Learndash\Stripe\Util\RequestOptions $opts
     *
     * @throws \StellarWP\Learndash\Stripe\Exception\ApiErrorException if the request fails
     *
     * @return \StellarWP\Learndash\Stripe\Review
     */
    public function approve($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/reviews/%s/approve', $id), $params, $opts);
    }

    /**
     * Retrieves a <code>Review</code> object.
     *
     * @param string $id
     * @param null|array $params
     * @param null|array|\StellarWP\Learndash\Stripe\Util\RequestOptions $opts
     *
     * @throws \StellarWP\Learndash\Stripe\Exception\ApiErrorException if the request fails
     *
     * @return \StellarWP\Learndash\Stripe\Review
     */
    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/reviews/%s', $id), $params, $opts);
    }
}

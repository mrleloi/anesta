<?php

// File generated from our OpenAPI spec

namespace StellarWP\Learndash\Stripe\Service\Issuing;

class CardService extends \StellarWP\Learndash\Stripe\Service\AbstractService
{
    /**
     * Returns a list of Issuing <code>Card</code> objects. The objects are sorted in
     * descending order by creation date, with the most recently created object
     * appearing first.
     *
     * @param null|array $params
     * @param null|array|\StellarWP\Learndash\Stripe\Util\RequestOptions $opts
     *
     * @throws \StellarWP\Learndash\Stripe\Exception\ApiErrorException if the request fails
     *
     * @return \StellarWP\Learndash\Stripe\Collection<\Stripe\Issuing\Card>
     *
     * @license MIT
     * Modified by learndash on 05-January-2024 using Strauss.
     * @see https://github.com/BrianHenryIE/strauss
     */
    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/v1/issuing/cards', $params, $opts);
    }

    /**
     * Creates an Issuing <code>Card</code> object.
     *
     * @param null|array $params
     * @param null|array|\StellarWP\Learndash\Stripe\Util\RequestOptions $opts
     *
     * @throws \StellarWP\Learndash\Stripe\Exception\ApiErrorException if the request fails
     *
     * @return \StellarWP\Learndash\Stripe\Issuing\Card
     */
    public function create($params = null, $opts = null)
    {
        return $this->request('post', '/v1/issuing/cards', $params, $opts);
    }

    /**
     * Retrieves an Issuing <code>Card</code> object.
     *
     * @param string $id
     * @param null|array $params
     * @param null|array|\StellarWP\Learndash\Stripe\Util\RequestOptions $opts
     *
     * @throws \StellarWP\Learndash\Stripe\Exception\ApiErrorException if the request fails
     *
     * @return \StellarWP\Learndash\Stripe\Issuing\Card
     */
    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/issuing/cards/%s', $id), $params, $opts);
    }

    /**
     * Updates the specified Issuing <code>Card</code> object by setting the values of
     * the parameters passed. Any parameters not provided will be left unchanged.
     *
     * @param string $id
     * @param null|array $params
     * @param null|array|\StellarWP\Learndash\Stripe\Util\RequestOptions $opts
     *
     * @throws \StellarWP\Learndash\Stripe\Exception\ApiErrorException if the request fails
     *
     * @return \StellarWP\Learndash\Stripe\Issuing\Card
     */
    public function update($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/issuing/cards/%s', $id), $params, $opts);
    }
}

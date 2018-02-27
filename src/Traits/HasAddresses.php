<?php namespace FairDigital\Addresses\Traits;

use FairDigital\Addresses\Models\Address;
use FairDigital\Addresses\Exceptions\FailedValidationException;

/**
 * Class HasAddresses
 * @package FairDigital\Addresses\Traits
 */
trait HasAddresses
{
    /**
     * Get all addresses for this model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function addresses()
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    /**
     * Check if model has an address.
     *
     * @return bool
     */
    public function hasAddress()
    {
        return (bool) $this->addresses()->count();
    }

    /**
     * Add an address to this model.
     *
     * @param  array  $attributes
     * @return mixed
     *
     * @throws \Exception
     */
    public function addAddress(array $attributes)
    {
        $attributes = $this->loadAddressAttributes($attributes);

        return $this->addresses()->updateOrCreate($attributes);
    }

    /**
     * Updates the given address.
     *
     * @param  Address  $address
     * @param  array    $attributes
     * @return bool
     *
     * @throws \Exception
     */
    public function updateAddress(Address $address, array $attributes)
    {
        $attributes = $this->loadAddressAttributes($attributes);

        return $address->fill($attributes)->save();
    }

    /**
     * Deletes given address.
     *
     * @param  Address  $address
     * @return bool
     *
     * @throws \Exception
     */
    public function deleteAddress(Address $address)
    {
        if ($this !== $address->addressable()->first())
            return false;

        return $address->delete();
    }

    /**
     * Deletes all the addresses of this model.
     *
     * @return bool
     */
    public function flushAddresses()
    {
        return $this->addresses()->delete();
    }

    /**
     * Get the primary address.
     *
     * @return Address
     */
    public function getPrimaryAddress()
    {
        return $this->addresses()->orderBy('is_primary', 'DESC')->first();
    }

    /**
     * Get the billing address.
     *
     * @return Address
     */
    public function getBillingAddress()
    {
        return $this->addresses()->orderBy('is_billing', 'DESC')->first();
    }

    /**
     * Get the shipping address.
     *
     * @return Address
     */
    public function getShippingAddress()
    {
        return $this->addresses()->orderBy('is_shipping', 'DESC')->first();
    }

    /**
     * Add country id to attributes array.
     *
     * @param  array  $attributes
     * @return array
     * @throws FailedValidationException
     */
    public function loadAddressAttributes(array $attributes)
    {
        // return if no country given
        if (! isset($attributes['country']))
            return $attributes;

        // run validation
        $validator = $this->validateAddress($attributes);

        if ($validator->fails())
            throw new FailedValidationException('Validator failed for: '. implode(', ', $attributes));

        // return attributes array with country_id key/value pair
        return $attributes;
    }

    /**
     * Validate the address.
     *
     * @param  array  $attributes
     * @return \Illuminate\Contracts\Validation\Validator
     */
    function validateAddress(array $attributes)
    {
        $rules = Address::getValidationRules();

        return validator($attributes, $rules);
    }
}
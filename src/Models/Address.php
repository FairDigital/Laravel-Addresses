<?php namespace FairDigital\Addresses\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Address
 * @package FairDigital\Addresses\Models
 */
class Address extends Model
{
    use SoftDeletes;

    /**
     * @inheritdoc
     */
    protected $fillable = [
        'street',
        'street_extra',
        'city',
        'state',
        'post_code',
        'country',
        'note',
        'lat',
        'lng',
        'addressable_id',
        'addressable_type',
        'is_primary',
        'is_billing',
        'is_shipping',
    ];

    /**
     * @inheritdoc
     */
    protected $dates = ['deleted_at'];

    /**
     * @inheritdoc
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = config('addresses.table', 'addresses');
    }

    /**
     * Get the related model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function addressable()
    {
        return $this->morphTo();
    }

    /**
     * {@inheritdoc}
     */
    public static function boot()
    {
        parent::boot();

        static::saving(function($address) {
            if (config('addresses.geocode', true))
                $address->geocode();
        });
    }

    /**
     * Get the validation rules.
     *
     * @return array
     */
    public static function getValidationRules()
    {
        $rules = [
            'street'       => 'required|string|min:3|max:60',
            'street_extra' => 'string|min:3|max:60|nullable',
            'city'         => 'required|string|min:3|max:60',
            'state'        => 'string|min:2|max:60',
            'post_code'    => 'required|min:4|max:10|AlphaDash',
            'country'   => 'required|string',
        ];

        foreach(config('addresses.flags', ['public', 'primary', 'billing', 'shipping']) as $flag)
            $rules['is_'.$flag] = 'boolean';

        return $rules;
    }

    /**
     * Try to fetch the coordinates from Google and store them.
     *
     * @return $this
     */
    public function geocode()
    {
        if(!$this->lat && !$this->lng) {
            // build query string
            $query = [];
            $query[] = $this->street       ?: '';
            $query[] = $this->street_extra ?: '';
            $query[] = $this->city         ?: '';
            $query[] = $this->state        ?: '';
            $query[] = $this->post_code    ?: '';
            $query[] = $this->getCountry() ?: '';

            // build query string
            $query = trim(implode(',', array_filter($query)));
            $query = str_replace(' ', '+', $query);

            if (! $query)
                return $this;

            // build url
            $url = 'https://maps.google.com/maps/api/geocode/json?address='. $query .'&sensor=false';

            // try to get geo codes
            if ($geocode = file_get_contents($url)) {
                $output = json_decode($geocode);

                if (count($output->results) && isset($output->results[0])) {
                    if ($geo = $output->results[0]->geometry) {
                        $this->lat = $geo->location->lat;
                        $this->lng = $geo->location->lng;
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Get the address as array.
     *
     * @return array
     */
    public function getArray()
    {
        $address = $two = [];

        $two[] = $this->post_code ?: '';
        $two[] = $this->city      ?: '';
        $two[] = $this->state     ?: '';

        $address[] = $this->street       ?: '';
        $address[] = $this->street_extra ?: '';
        $address[] = $this->city      ?: '';
        $address[] = $this->post_code ?: '';
        $address[] = $this->state     ?: '';
        $address[] = $this->getCountry() ?: '';

        if (count($address = array_filter($address)) > 0)
            return $address;

        return null;
    }

    /**
     * Get the address as html block.
     *
     * @return string
     */
    public function getHtml()
    {
        if ($address = $this->getArray())
            return '<address>'. implode('<br />', array_filter($address)) .'</address>';

        return null;
    }

    /**
     * Get the address as a simple line.
     *
     * @return string
     */
    public function getLine()
    {
        if ($address = $this->getArray())
            return implode(', ', array_filter($address));

        return null;
    }

    /**
     * Get the country name.
     *
     * @return string|null
     */
    public function getCountry()
    {
        if ($this->country)
            return country($this->country)->getName();

        return null;
    }
}

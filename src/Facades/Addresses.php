<?php namespace FairDigital\Addresses\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Addresses
 * @package FairDigital\Addresses\Facades
 */
class Addresses extends Facade
{
    /**
     * @inheritdoc
     */
    protected static function getFacadeAccessor()
    {
        return 'addresses';
    }
}
<?php

return [
    /*
     * Main table
     */
    'table' => 'addresses',

    /*
     * Flag columns to be added to table
     */
    'flags' => ['public', 'primary', 'billing', 'shipping'],

    /*
     * Enable geocoding to add coordinates (lon/lat) to addresses
     */
    'geocode' => true,

];
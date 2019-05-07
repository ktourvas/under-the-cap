<?php

namespace UnderTheCap\Exceptions;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

class PromoConfigurationException extends Exception
{
    /**
     * The promotion instance.
     *
     * @var \UnderTheCap\Promo
     */
    public $promo;

    /**
     * Create a new exception instance.
     *
     * @param  string  $errorBag
     * @return void
     */
    public function __construct($errorBag = 'Some part of the module configuration is not valid')
    {
        parent::__construct($errorBag);
    }

    /**
     * Get all of the validation error messages.
     *
     * @return array
     */
    public function error()
    {
        return [ 'error' => 'invalid_config' ];
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return response($this->error(), 422);
    }

}

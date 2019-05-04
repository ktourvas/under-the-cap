<?php

namespace UnderTheCap\Exceptions;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

class PromoStatusException extends Exception
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
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @param  string  $errorBag
     * @return void
     */
    public function __construct($promo)
    {
        parent::__construct('The given data was invalid.');

        $this->promo = $promo;
    }

    /**
     * Get all of the validation error messages.
     *
     * @return array
     */
    public function error()
    {
        return [ 'error' => 'promo_'.( ($this->promo->status() == 'e') ? 'closed' : 'pending') ];
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

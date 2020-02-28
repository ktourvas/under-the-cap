<?php

namespace UnderTheCap\Exceptions;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

class RedemptionCodeException extends Exception
{

    /**
     * Get all of the validation error messages.
     *
     * @return array
     */
    public function error()
    {
        return [
            'errors' => [
                'code' => [
                    'Ο κωδικός δεν είναι έγκυρος'
                ]
            ]
        ];
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

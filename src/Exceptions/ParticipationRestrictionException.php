<?php

namespace UnderTheCap\Exceptions;

use Exception;
use Illuminate\Support\Arr;

class ParticipationRestrictionException extends Exception
{

    /**
     * Get all of the validation error messages.
     *
     * @return array
     */
    public function error()
    {
        return [
            'error' => 'participation_restriction',
            'errors' => [
                'email' => [
                    'The email submitted is already associated with a participation.'
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

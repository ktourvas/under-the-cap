<?php

namespace UnderTheCap\Events;

use Illuminate\Queue\SerializesModels;
use UnderTheCap\Participation;

class ParticipationSubmitted
{
    use SerializesModels;

    public $participation;

    /**
     * Create a new event instance.
     *
     * @param  \UnderTheCap\Participation $participation
     * @return void
     */
    public function __construct(Participation $participation)
    {
        $this->participation = $participation;
    }
}
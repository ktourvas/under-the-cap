<?php

namespace UnderTheCap\Events;

use Illuminate\Queue\SerializesModels;
use UnderTheCap\Entities\Participation;

class ParticipationSubmitted
{
    use SerializesModels;

    public $participation;

    /**
     * Create a new event instance.
     *
     * @param  \UnderTheCap\Entities\Participation $participation
     * @return void
     */
    public function __construct(Participation $participation)
    {
        $this->participation = $participation;
    }
}
<?php

class ParticipationTest extends Orchestra\Testbench\TestCase {

    protected function getEnvironmentSetUp($app)
    {

        $app['config']->set('under-the-cap', require_once (__DIR__.'/../../config/under-the-cap.php'));

    }

    public function testConfig()
    {
//        $participation = new \UnderTheCap\Participation();
        $this->assertFalse(false);
    }

}
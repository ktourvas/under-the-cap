<?php

//class ParticipationTest extends \PHPUnit\Framework\TestCase {
class ParticipationTest extends Orchestra\Testbench\TestCase {

    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:

        $app['config']->set('under-the-cap', require_once (__DIR__.'/../../config/under-the-cap.php'));

    }

    public function testConfig() {
        $participaiton = new \UnderTheCap\Participation();
        $this->assertFalse(false);
    }

}
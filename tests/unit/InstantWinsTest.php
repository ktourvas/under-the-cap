<?php

//class ParticipationTest extends \PHPUnit\Framework\TestCase {
class InstantWinsTest extends Orchestra\Testbench\TestCase {

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // and other test setup steps you need to perform
        $this->promo = new UnderTheCap\Promo();
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('under-the-cap', include __DIR__.'/../../config/under-the-cap.php' );
    }

    public function testConfig() {
        $instant = new UnderTheCap\Invokable\InstantWinsManager($this->promo);
        $this->assertTrue(is_callable($instant));
    }

    public function testInstantWin() {

        $instant = new UnderTheCap\Invokable\InstantWinsManager();

        foreach($this->promo->instantDraws() as $id => $info) {

            for ($i=0; $i<=100000; $i++){
                $instant($id, $info);
            }

        }

//        $instant = new UnderTheCap\Invokable\InstantWinsManager($this->promo);
//        $win = $instant();
//        $this->assertIsArray($win);
//        $this->assertTrue($win['win']);
//        $this->assertInstanceOf(\UnderTheCap\Entities\Present::class, $win['present']);

    }

//    public function testInstantLoss() {
//
//    }

//    /**
//     * Setup the test environment.
//     */
//    protected function setUp(): void
//    {
//        parent::setUp();
//
//        // and other test setup steps you need to perform
////        $this->promo = new UnderTheCap\Promo();
//    }
//
//    protected function getEnvironmentSetUp($app)
//    {
//        $app['config']->set('under-the-cap', require_once (__DIR__.'/../../config/under-the-cap.php'));
//    }
//
//    public function testConfig()
//    {
////        $participation = new \UnderTheCap\Participation();
//        $this->assertFalse(false);
////        $instant = new UnderTheCap\Invokable\InstantWinsManager(new UnderTheCap\Promo());
////        $this->assertTrue(is_callable($instant));
//
//    }

}
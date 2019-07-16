<?php

class PromosTest extends Orchestra\Testbench\TestCase {

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // and other test setup steps you need to perform
        $this->promos = new UnderTheCap\Promos();
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('under-the-cap', include __DIR__.'/../../config/under-the-cap.php' );

    }

    /** @test */
    public function testPromos() {

        $this->assertInstanceOf('UnderTheCap\Promo', $this->promos->promo('current'));

        $this->assertNull( $this->promos->promo('dsdgrewwdasafew') );

        $this->assertTrue( $this->promos->setCurrent('current') );

        $this->assertFalse( $this->promos->setCurrent('sfdfrfdsfsdsf') );

    }

}
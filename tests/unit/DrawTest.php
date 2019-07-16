<?php

class DrawTest extends Orchestra\Testbench\TestCase {

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // and other test setup steps you need to perform
//        $this->promo = new UnderTheCap\Promo();
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('under-the-cap', include __DIR__.'/../../config/under-the-cap.php' );

    }

    /** @test */
    public function configInfo()
    {
        $this->assertTrue(true);
//        $this->assertIsArray($this->promo->participationFields());
//        $this->assertIsArray($this->promo->participationFieldKeys());
//
//        $this->assertInstanceOf('Illuminate\Support\Collection', $this->promo->ParticipationSearchables());
//        $this->assertInstanceOf('Illuminate\Support\Collection', $this->promo->participationValidationRules());
//        $this->assertInstanceOf('Illuminate\Support\Collection', $this->promo->participationValidationMessages());

    }

}
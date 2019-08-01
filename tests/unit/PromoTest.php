<?php

class PromoTest extends Orchestra\Testbench\TestCase {

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // and other test setup steps you need to perform
        $this->promos = new UnderTheCap\Promos();

        $this->promo = $this->promos->promo('current');


//        $this->promo = new UnderTheCap\Promo();

        $this->promos = $this->app->make('UnderTheCap\Promos');

    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('under-the-cap', include __DIR__.'/../../config/under-the-cap.php' );



    }

    /** @test */
    public function configInfo()
    {

        $this->assertIsArray($this->promo->participationFields());
        $this->assertIsArray($this->promo->participationFieldKeys());

        $this->assertInstanceOf('Illuminate\Support\Collection', $this->promo->ParticipationSearchables());
        $this->assertInstanceOf('Illuminate\Support\Collection', $this->promo->participationValidationRules());
        $this->assertInstanceOf('Illuminate\Support\Collection', $this->promo->participationValidationMessages());

        $this->assertIsArray($this->promos->promo('current')->participationFields());
        $this->assertIsArray($this->promos->promo('current')->participationFieldKeys());

        $this->assertInstanceOf('Illuminate\Support\Collection', $this->promos->promo('current')->ParticipationSearchables());
        $this->assertInstanceOf('Illuminate\Support\Collection', $this->promos->promo('current')->participationValidationRules());
        $this->assertInstanceOf('Illuminate\Support\Collection', $this->promos->promo('current')->participationValidationMessages());

    }

    public function testDrawsConfigNormal()
    {
        $this->assertInstanceOf('Illuminate\Support\Collection', $this->promo->draws());

        $this->assertInstanceOf('Illuminate\Support\Collection', $this->promos->promo('current')->draws());

    }

    public function testDrawReturnsArray()
    {

        $this->assertIsArray($this->promo->draw(1));

        $this->assertIsArray($this->promos->promo('current')->draw(1));

    }

    public function testStatusRunning()
    {
        $this->assertEquals('r', $this->promo->status());

        $this->assertEquals(null, $this->promo->validatePromoStatus() );


        $this->assertEquals('r', $this->promos->promo('current')->status());

        $this->assertEquals(null, $this->promos->promo('current')->validatePromoStatus() );


    }

    public function testStatusPending()
    {

        $this->app['config']->set( 'under-the-cap.current.start_date', date('Y-m-d H:i:s', time() + 86400) );
        $this->promo = new UnderTheCap\Promo();

        $this->assertEquals($this->promo->status(), 'p');

        $this->expectException(\UnderTheCap\Exceptions\PromoStatusException::class);
        $this->promo->validatePromoStatus();

    }

    public function testStatusPendingThroughPromos()
    {

        $this->app['config']->set( 'under-the-cap.current.start_date', date('Y-m-d H:i:s', time() + 86400) );
        $this->promos = new UnderTheCap\Promos();

        $this->assertEquals($this->promos->promo('current')->status(), 'p');

        $this->expectException(\UnderTheCap\Exceptions\PromoStatusException::class);
        $this->promos->promo('current')->validatePromoStatus();

    }

    public function testStatusEnded()
    {

        $this->app['config']->set( 'under-the-cap.current.start_date', date('Y-m-d H:i:s', time() - ( 2*86400 ) ) );
        $this->app['config']->set( 'under-the-cap.current.end_date', date('Y-m-d H:i:s', time() - 86400 ) );
        $this->promo = new UnderTheCap\Promo();

        $this->assertEquals($this->promo->status(), 'e');

        $this->expectException(\UnderTheCap\Exceptions\PromoStatusException::class);
        $this->promo->validatePromoStatus();

    }

    public function testStatusEndedThroughPromos()
    {

        $this->app['config']->set( 'under-the-cap.current.start_date', date('Y-m-d H:i:s', time() - ( 2*86400 ) ) );
        $this->app['config']->set( 'under-the-cap.current.end_date', date('Y-m-d H:i:s', time() - 86400 ) );
        $this->promos = new UnderTheCap\Promos();

        $this->assertEquals($this->promos->promo('current')->status(), 'e');

        $this->expectException(\UnderTheCap\Exceptions\PromoStatusException::class);
        $this->promos->promo('current')->validatePromoStatus();

    }

}
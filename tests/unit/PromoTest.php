<?php

//class ParticipationTest extends \PHPUnit\Framework\TestCase {
class PromoTest extends Orchestra\Testbench\TestCase {

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


    /** @test */
    public function configInfo()
    {

        $this->assertIsArray($this->promo->participationFields());
        $this->assertIsArray($this->promo->participationFieldKeys());

        $this->assertInstanceOf('Illuminate\Support\Collection', $this->promo->ParticipationSearchables());
        $this->assertInstanceOf('Illuminate\Support\Collection', $this->promo->participationValidationRules());
        $this->assertInstanceOf('Illuminate\Support\Collection', $this->promo->participationValidationMessages());

    }

    public function testDrawsConfigNormal()
    {
        $this->assertIsArray($this->promo->draws());
    }

    public function testDrawReturnsArray()
    {
        $this->assertIsArray($this->promo->draw(1));
    }

    public function testDrawsRecursiveConfigException()
    {
        $this->app['config']->set( 'under-the-cap.current.draws.recursive', [
            0 => [
                'onewrongkey' => 'saskajsaksasa'
            ]
        ] );

        $this->promo = new UnderTheCap\Promo();

        $this->expectException(\UnderTheCap\Exceptions\PromoConfigurationException::class);

        $this->promo->draws();

    }

    public function testDrawsAdhocConfigException()
    {
        $this->app['config']->set( 'under-the-cap.current.draws.adhoc', [
            0 => [
                'onewrongkey' => 'saskajsaksasa'
            ]
        ] );

        $this->promo = new UnderTheCap\Promo();

        $this->expectException(\UnderTheCap\Exceptions\PromoConfigurationException::class);

        $this->promo->draws();

    }


    public function testStatusRunning()
    {
        $this->assertEquals('r', $this->promo->status());

        $this->assertEquals(null, $this->promo->validatePromoStatus() );

    }

    public function testStatusPending()
    {

        $this->app['config']->set( 'under-the-cap.current.start_date', date('Y-m-d H:i:s', time() + 86400) );
        $this->promo = new UnderTheCap\Promo();
        $this->assertEquals($this->promo->status(), 'p');

        $this->expectException(\UnderTheCap\Exceptions\PromoStatusException::class);
        $this->promo->validatePromoStatus();

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

}
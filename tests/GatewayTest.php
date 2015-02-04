<?php

namespace Omnipay\Fatzebra;

use Omnipay\Tests\GatewayTestCase;
use Omnipay\Common\CreditCard;

class GatewayTest extends GatewayTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->gateway = new FatzebraGateway($this->getHttpClient(), $this->getHttpRequest());

        $this->card = new CreditCard(array(
            'firstName' => 'Example',
            'lastName' => 'User',
            'number' => '4111111111111111',
            'expiryMonth' => '12',
            'expiryYear' => '2016',
            'cvv' => '123',
        ));
        $this->options = array(
            'amount' => '10.00',
            'transactionReference' => '123412341234',
            'card' => $this->card,
        );
    }

    public function testPurchase()
    {
        $this->setMockHttpResponse('PurchaseSuccess.txt');

        $response = $this->gateway->purchase($this->options)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('525-P-S2Y05UQ9', $response->getTransactionReference());
        $this->assertEmpty($response->getMessage());
    }

    public function testFetchTransaction()
    {
        $request = $this->gateway->fetchTransaction(array('transactionReference' => '525-P-S2Y05UQ9'));

        $this->assertInstanceOf('\Omnipay\Fatzebra\Message\FetchTransactionRequest', $request);
        $this->assertSame('525-P-S2Y05UQ9', $request->getTransactionReference());
    }

    public function testRefund()
    {
        $this->setMockHttpResponse('RefundSuccess.txt');

        $response = $this->gateway->refund(array(
            'transactionReference'  => "525-P-S2Y05UQ9",
            'amount'                => 10.00,
        ))->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('525-R-XIVU3L9U', $response->getTransactionReference());
        $this->assertEmpty($response->getMessage());
    }

    public function testCreateCard()
    {
        $this->setMockHttpResponse('CreateCardSuccess.txt');

        $response = $this->gateway->createCard(array(
            'card'      => $this->card,
        ))->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('hmx8o839', $response->getTransactionReference());
        $this->assertEmpty($response->getMessage());
    }

    public function testCreatePlan()
    {
        $this->setMockHttpResponse('CreatePlanSuccess.txt');

        $response = $this->gateway->createPlan(array(
            "name"                  => "Gold Membership",
            "amount"                => 100.00,
            "transactionReference"  => "Gold-1",
            "description"           => "Gold level membership",
        ))->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('525-PL-IN37ICBK', $response->getTransactionReference());
        $this->assertEmpty($response->getMessage());
    }

    public function testFetchAllPlans()
    {
        $this->setMockHttpResponse('FetchAllPlansSuccess.txt');

        $response = $this->gateway->fetchAllPlans()->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertEmpty($response->getMessage());
    }

    public function testFetchPlan()
    {
        $this->setMockHttpResponse('FetchPlanSuccess.txt');

        $response = $this->gateway->fetchPlan(array(
           'transactionReference'  => '525-PL-IN37ICBK',
        ))->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('525-PL-IN37ICBK', $response->getTransactionReference());
        $this->assertEmpty($response->getMessage());
    }
}

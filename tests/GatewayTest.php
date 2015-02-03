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

        $this->options = array(
            'amount' => '10.00',
            'transactionReference' => '123412341234',
            'card' => new CreditCard(array(
                'firstName' => 'Example',
                'lastName' => 'User',
                'number' => '4111111111111111',
                'expiryMonth' => '12',
                'expiryYear' => '2016',
                'cvv' => '123',
            )),
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

}

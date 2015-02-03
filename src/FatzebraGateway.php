<?php
/**
 * Fat Zebra / Paystream Gateway
 */

namespace Omnipay\Fatzebra;

use Omnipay\Common\AbstractGateway;

/**
 * Fat Zebra / Paystream Gateway
 *
 * Example:
 *
 * <code>
 *   // Create a gateway for the Fat Zebra REST Gateway
 *   // (routes to GatewayFactory::create)
 *   $gateway = Omnipay::create('FatzebraGateway');
 *
 *   // Initialise the gateway
 *   $gateway->initialize(array(
 *       'username' => 'TESTasdfasdfasdf',
 *       'token'    => 'asdfasdfasdfasdfasdf',
 *       'testMode' => true, // Or false when you are ready for live transactions
 *   ));
 *
 *   // Create a credit card object
 *   // This card can be used for testing.
 *   $card = new CreditCard(array(
 *               'firstName'    => 'Example',
 *               'lastName'     => 'Customer',
 *               'number'       => '4005550000000001',
 *               'expiryMonth'  => '01',
 *               'expiryYear'   => '2020',
 *               'cvv'          => '123',
 *   ));
 *
 *   // Do a purchase transaction on the gateway
 *   $transaction = $gateway->purchase(array(
 *       'amount'                   => '10.00',
 *       'transactionReference'     => 'TestPurchaseTransaction',
 *       'clientIp'                 => $_SERVER['REMOTE_ADDR'],
 *       'card'                     => $card,
 *   ));
 *   $response = $transaction->send();
 *   if ($response->isSuccessful()) {
 *       echo "Purchase transaction was successful!\n";
 *       $sale_id = $response->getTransactionReference();
 *       echo "Transaction reference = " . $sale_id . "\n";
 *   }
 * </code>
 *
 * Test modes:
 *
 * There are two test modes in the Paystream system - one is a
 * sandbox environment and the other is a test mode flag.
 *
 * The Sandbox Environment is an identical copy of the live environment
 * which is 100% functional except for communicating with the banks.
 *
 * The Test Mode Flag is used to switch the live environment into
 * test mode. If test: true is sent with your request your transactions
 * will be executed in the live environment, but not communicate with
 * the bank backends. This mode is useful for testing changes to your
 * live website.
 *
 * Currently this class makes the assumption that if the testMode
 * flag is set then the Sandbox Environment is being used.
 *
 * @see \Omnipay\Common\AbstractGateway
 * @see \Omnipay\Fatzebra\Message\AbstractRestRequest
 * @link http://www.paystream.com.au/developer-guides/
 * @link https://www.fatzebra.com.au/
 */
class FatzebraGateway extends AbstractGateway
{
    public $transparentRedirect = true;

    /**
     * Get the gateway display name
     *
     * @return string
     */
    public function getName()
    {
        return 'Fat Zebra v1.0';
    }

    /**
     * Get the gateway default parameters
     *
     * @return array
     */
    public function getDefaultParameters()
    {
        return array(
            'username' => '',
            'token' => '',
            'testMode' => false,
        );
    }

    /**
     * Get the gateway username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->getParameter('username');
    }

    /**
     * Set the gateway username
     *
     * Note that all test usernames begin with the word TEST in upper case.
     *
     * @return FatzebraGateway provides a fluent interface.
     */
    public function setUsername($value)
    {
        return $this->setParameter('username', $value);
    }

    /**
     * Get the gateway token -- used as the password in HTTP Basic Auth
     *
     * @return string
     */
    public function getToken()
    {
        return $this->getParameter('token');
    }

    /**
     * Set the gateway token -- used as the password in HTTP Basic Auth
     *
     * @return FatzebraGateway provides a fluent interface.
     */
    public function setToken($value)
    {
        return $this->setParameter('token', $value);
    }

    /**
     * Create a purchase request.
     *
     * @param array $parameters
     * @return \Omnipay\Fatzebra\Message\PurchaseRequest
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Fatzebra\Message\PurchaseRequest', $parameters);
    }

    /**
     * Fetch a purchase
     *
     * @param array $parameters
     * @return \Omnipay\Fatzebra\Message\FetchTransactionRequest
     */
    public function fetchTransaction(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Fatzebra\Message\FetchTransactionRequest', $parameters);
    }

    /**
     * Refund a purchase
     *
     * @param array $parameters
     * @return \Omnipay\Fatzebra\Message\RefundRequest
     */
    public function refund(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Fatzebra\Message\RefundRequest', $parameters);
    }
}

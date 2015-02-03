<?php
/**
 * Fat Zebra Abstract REST Request
 */

namespace Omnipay\Fatzebra\Message;

use Guzzle\Http\EntityBody;

/**
 * Fat Zebra Abstract REST Request
 *
 * @link http://www.paystream.com.au/developer-guides/
 */
abstract class AbstractRestRequest extends \Omnipay\Common\Message\AbstractRequest
{
    const API_VERSION = 'v1.0';

    /**
     * Sandbox Endpoint URL
     *
     * The PayPal REST APIs are supported in two environments. Use the Sandbox environment
     * for testing purposes, then move to the live environment for production processing.
     * When testing, generate an access token with your test credentials to make calls to
     * the Sandbox URIs. When you’re set to go live, use the live credentials assigned to
     * your app to generate a new access token to be used with the live URIs.
     *
     * @var string URL
     */
    protected $testEndpoint = 'https://gateway.sandbox.fatzebra.com.au';

    /**
     * Live Endpoint URL
     *
     * When you’re set to go live, use the live credentials assigned to
     * your app to generate a new access token to be used with the live URIs.
     *
     * @var string URL
     */
    protected $liveEndpoint = 'https://gateway.fatzebra.com.au';

    /**
     * Get HTTP Method.
     *
     * This is nearly always POST but can be over-ridden in sub classes.
     *
     * @return string
     */
    protected function getHttpMethod()
    {
        return 'POST';
    }

    protected function getEndpoint()
    {
        $base = $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
        return $base . '/' . self::API_VERSION;
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
     * @return AbstractRestRequest provides a fluent interface.
     */
    public function setUsername($value)
    {
        return $this->setParameter('username', $value);
    }

    public function sendData($data)
    {
        // don't throw exceptions for 4xx errors
        $this->httpClient->getEventDispatcher()->addListener(
            'request.error',
            function ($event) {
                if ($event['response']->isClientError()) {
                    $event->stopPropagation();
                }
            }
        );

        // Guzzle HTTP Client createRequest does funny things when a GET request
        // has attached data, so don't send the data if the method is GET.
        if ($this->getHttpMethod() == 'GET') {
            $httpRequest = $this->httpClient->createRequest(
                $this->getHttpMethod(),
                $this->getEndpoint(),
                array(
                    'Accept'         => 'application/json',
                )
            )->setAuth($this->getUsername(), $this->getToken());
        } else {
            $httpRequest = $this->httpClient->createRequest(
                $this->getHttpMethod(),
                $this->getEndpoint(),
                array(
                    'Accept'         => 'application/json',
                    'Content-type'   => 'application/json',
                ),
                json_encode($data)
            )->setAuth($this->getUsername(), $this->getToken());
        }
        
        // Might be useful to have some debug code here.  Perhaps hook to whatever
        // logging engine is being used.
        // echo "Data == " . json_encode($data) . "\n";

        $httpResponse = $httpRequest->send();

        return $this->response = new RestResponse($this, $httpResponse->json(), $httpResponse->getStatusCode());
    }
}

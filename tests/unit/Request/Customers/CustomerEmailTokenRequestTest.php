<?php
/**
 * @author @ct-jensschulze <jens.schulze@commercetools.de>
 */

namespace Sphere\Core\Request\Customers;


use Sphere\Core\Client\HttpMethod;
use Sphere\Core\RequestTestCase;

/**
 * Class CustomerEmailTokenRequestTest
 * @package Sphere\Core\Request\Customers
 */
class CustomerEmailTokenRequestTest extends RequestTestCase
{
    const CUSTOMER_EMAIL_TOKEN_REQUEST = '\Sphere\Core\Request\Customers\CustomerEmailTokenRequest';

    public function testHttpRequestMethod()
    {
        $request = CustomerEmailTokenRequest::ofIdVersionAndTtl('customerId', 1, 5);
        $httpRequest = $request->httpRequest();

        $this->assertSame(HttpMethod::POST, $httpRequest->getMethod());
    }

    public function testHttpRequestPath()
    {
        $request = CustomerEmailTokenRequest::ofIdVersionAndTtl('customerId', 1, 5);
        $httpRequest = $request->httpRequest();

        $this->assertSame('customers/email-token', (string)$httpRequest->getUri());
    }

    public function testHttpRequestObject()
    {
        $request = CustomerEmailTokenRequest::ofIdVersionAndTtl('customerId', 1, 5);
        $httpRequest = $request->httpRequest();

        $this->assertJsonStringEqualsJsonString(
            json_encode(['id' => 'customerId', 'version' => 1, 'ttlMinutes' => 5]),
            (string)$httpRequest->getBody()
        );
    }
}

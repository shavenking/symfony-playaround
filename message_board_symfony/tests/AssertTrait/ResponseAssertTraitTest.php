<?php

namespace Tests\AssertTrait;

use Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;

class ResponseAssertTraitTest extends TestCase
{
    /**
     * ResponseAssertTrait#getResponse
     * should return Response
     * after request sent.
     */
    public function testGetResponse()
    {
        $this->mockResponse();

        // getResponse should return Response
        $response = $this->getResponse();

        $this->assertSame(
            Response::class,
            get_class($response)
        );
    }

    /**
     * @depends testGetResponse
     */
    public function testAssertStatusCodeEquals()
    {
        $this->mockResponse();

        $this->assertStatusCodeEquals(200);

        try {
            $this->assertStatusCodeEquals(201);
        } catch (\PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @depends testGetResponse
     */
    public function testAssertStatusOk()
    {
        $this->mockResponse();

        $this->assertStatusOk();

        try {
            $this->getMockedResponse()->setStatusCode(201);

            $this->assertStatusOk();
        } catch (\PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @depends testGetResponse
     */
    public function testAssertContentContains()
    {
        $this->mockResponse();
        $this->getMockedResponse()->setContent('testing make awesome');

        $this->assertContentContains('testing');
        $this->assertContentContains(['testing', 'awesome']);

        try {
            $this->assertContentContains('impossible to be found');
        } catch (\PHPUnit_Framework_AssertionFailedError $e) {
            // assertion passes
        }

        try {
            $this->assertContentContains(['impossible', 'to', 'be', 'found']);
        } catch (\PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    protected function mockClient()
    {
        $this->client = $this->getMockBuilder(get_class($this->client))
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function mockResponse(Response $response = null)
    {
        // set default response
        if (is_null($response)) {
            $response = new Response;
        }

        // mock client if it's not mocked yet
        if (!$this->client instanceof \PHPUnit_Framework_MockObject_MockObject) {
            $this->mockClient();
        }

        // mock response
        $this->client->method('getResponse')->willReturn($response);
    }

    protected function getMockedResponse()
    {
        return $this->getResponse();
    }
}

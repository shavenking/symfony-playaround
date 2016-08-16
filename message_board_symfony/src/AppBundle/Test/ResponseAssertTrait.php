<?php

namespace AppBundle\Test;

trait ResponseAssertTrait
{
    protected function assertContentContains($needle)
    {
        $content = $this->getResponse()->getContent();

        if (is_array($needle)) {
            foreach ($needle as $candidate) {
                $this->assertContains($candidate, $content);
            }

            return;
        }

        $this->assertContains($needle, $content);
    }

    protected function assertStatusOk()
    {
        $this->assertStatusCodeEquals(200);
    }

    protected function assertStatusCodeEquals($statusCode)
    {
        $this->assertEquals(
            $statusCode,
            $this->getResponse()->getStatusCode()
        );
    }

    protected function getResponse()
    {
        return $this->client->getResponse();
    }
}

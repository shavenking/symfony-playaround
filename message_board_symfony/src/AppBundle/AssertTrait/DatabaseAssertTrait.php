<?php

namespace AppBundle\AssertTrait;

trait DatabaseAssertTrait
{
    protected function assertExistInDatabase($entityName, $criteria, $invert = false)
    {
        $em = $this->getEntityManager();

        $result = $em->getRepository($entityName)->findOneBy($criteria);

        $constraint = $this->isNull();

        if (!$invert) {
            $constraint = $this->logicalNot($constraint);
        }

        $this->assertThat($result, $constraint);
    }

    protected function assertNotExistInDatabase($entityName, $criteria)
    {
        $this->assertExistInDatabase($entityName, $criteria, true);
    }

    protected function getEntityManager()
    {
        return $this->em;
    }
}

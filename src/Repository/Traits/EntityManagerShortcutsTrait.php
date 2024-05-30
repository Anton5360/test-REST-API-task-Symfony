<?php

namespace App\Repository\Traits;

trait EntityManagerShortcutsTrait
{
    protected function removeAndFlush(object $entity): void
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

    protected function persistAndFlush(object $entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }
}

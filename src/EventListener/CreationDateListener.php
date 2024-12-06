<?php

namespace App\EventListener;

use Doctrine\ORM\Event\PrePersistEventArgs;
use App\Entity\Advert;
use App\Entity\Picture;

class CreationDateListener
{
    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();

        // Vérifie si l'entité est Advert ou Picture
        if ($entity instanceof Advert || $entity instanceof Picture) {
            if ($entity->getCreatedAt() === null) {
                $entity->setCreatedAt(new \DateTime());
            }
        }
    }
}

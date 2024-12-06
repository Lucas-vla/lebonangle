<?php

namespace App\EventListener;

use App\Entity\AdminUser;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping\PostUpdate;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PasswordEncoderListener
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        $this->encodePassword($args->getObject(), $args);
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $this->encodePassword($args->getObject(), $args);
    }

    private function encodePassword(object $entity, $args): void
    {
        if (!$entity instanceof AdminUser) {
            return;
        }

        if ($plainPassword = $entity->getPlainPassword()) {
            $hashedPassword = $this->passwordHasher->hashPassword($entity, $plainPassword);
            $entity->setPassword($hashedPassword);
            $entity->eraseCredentials();

            // Si c'est un PreUpdate, il faut recalculer le changement
            if ($args instanceof PreUpdateEventArgs) {
                $em = $args->getEntityManager();
                $metadata = $em->getClassMetadata(get_class($entity));
                $em->getUnitOfWork()->recomputeSingleEntityChangeSet($metadata, $entity);
            }
        }
    }
}

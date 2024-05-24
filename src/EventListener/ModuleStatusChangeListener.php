<?php

// src/EventListener/ModuleStatusChangeListener.php

namespace App\EventListener;

use App\Entity\Module;

use App\EventSubscriber\ModuleStatusChangeEvent;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Doctrine\ORM\EntityManagerInterface;

use Doctrine\Common\EventSubscriber;


class ModuleStatusChangeListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            Events::postUpdate => 'onPostUpdate',
        ];
    }

    public function onPostUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        $entityManager = $args->getEntityManager();

        if ($entity instanceof Module && $entityManager->getUnitOfWork()->isEntityScheduled($entity)) {
            $changeset = $entityManager->getUnitOfWork()->getEntityChangeSet($entity);

            if (isset($changeset['IsOperating'])) {
                $newIsOperating = $changeset['IsOperating'][1];
                if ($newIsOperating !== $entity->isIsOperating()) {
                    $event = new ModuleStatusChangeEvent($entity);
                    $dispatcher = $entityManager->getEventManager()->getListeners()[Events::postUpdate][0];
                    $dispatcher->dispatch($event);
                }
            }
        }
    }
}
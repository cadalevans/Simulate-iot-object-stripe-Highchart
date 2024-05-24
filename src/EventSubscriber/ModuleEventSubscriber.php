<?php


// src/EventSubscriber/ModuleEventSubscriber.php

namespace App\EventSubscriber;



use App\Entity\Module;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs as ORM_LifecycleEventArgs; // Alias to resolve naming conflict
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ModuleEventSubscriber implements EventSubscriber
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function getSubscribedEvents(): array
    {
        return [
            'postPersist',
            'postUpdate',
        ];
    }

    public function postPersist(ORM_LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof Module) {
            $isOperating = $entity->getIsOperating();
            if ($isOperating === true) {
                $this->session->getFlashBag()->add('success', 'Module is now operating.');
            } elseif ($isOperating === false) {
                $this->session->getFlashBag()->add('warning', 'Module is no longer operating.');
            }
        }
    }

    public function postUpdate(ORM_LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof Module) {
            $isOperating = $entity->getIsOperating();
            if ($isOperating === true) {
                $this->session->getFlashBag()->add('success', 'Module is now operating.');
            } elseif ($isOperating === false) {
                $this->session->getFlashBag()->add('warning', 'Module is no longer operating.');
            }
        }
    }
}

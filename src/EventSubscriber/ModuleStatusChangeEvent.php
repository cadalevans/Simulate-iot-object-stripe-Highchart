<?php


// src/Event/ModuleStatusChangeEvent.php
namespace App\EventSubscriber;

use App\Entity\Module;
use Symfony\Contracts\EventDispatcher\Event;

class ModuleStatusChangeEvent extends Event
{
    public const NAME = 'module.status_change';

    private $module;

    public function __construct(Module $module)
    {
        $this->module = $module;
    }

    public function getModule(): Module
    {
        return $this->module;
    }
}

<?php

namespace App\EventListener\Entity;

use App\Entity\Extra\HasUuid;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

/**
 * Class UuidListener
 * @package App\EventListener\Entity
 */
class UuidListener
{
    /**
     * @param LifecycleEventArgs $event
     */
    public function prePersist(LifecycleEventArgs $event)
    {
//        if (in_array(HasUuid::class, array_keys(class_uses($event->getObject())))) { //TODO discuss (wft)!!!

            if (method_exists($event->getObject(), 'setUuid') && $event->getObject()->getUuid() === null) {

                $event->getObject()->setUuid();
            }
//        }
    }

}
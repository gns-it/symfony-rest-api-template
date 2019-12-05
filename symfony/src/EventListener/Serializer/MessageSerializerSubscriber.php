<?php
/**
 * @author Sergey Hashimov
 */

namespace App\EventListener\Serializer;

use App\Entity\Messenger\Message;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use Symfony\Component\Security\Core\Security;

/**
 * Class MessageSerializerSubscriber
 * @package App\EventListener\Serializer
 */
class MessageSerializerSubscriber implements EventSubscriberInterface
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            [
                'event' => 'serializer.pre_serialize',
                'method' => 'onPreSerialize',
                'class' => Message::class,
            ],
        ];
    }

    /**
     * @param PreSerializeEvent $event
     */
    public function onPreSerialize(PreSerializeEvent $event)
    {
        $c = 1;
        /** @var Message $mess */
        $mess = $event->getObject();
        if (in_array($this->security->getUser()->getUuid(), $mess->getReadBy())) {
            $mess->setIsReadByMe(true);
            $c++;
        }
        if (count($event->getObject()->getReadBy()) >= $c) {
            $mess->setIsReadByOpponent(true);
        }
    }
}
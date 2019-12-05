<?php
/**
 * @author Sergey Hashimov <hashimov.sergey@gmail.com>
 */

namespace App\EventListener\Filtration;


use Doctrine\ORM\QueryBuilder;
use Gns\GnsFilterBundle\Filtration\QueryBuilderManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Knp\Component\Pager\Event\ItemsEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class KnpPaginationSubscriber
 * @package App\Pagination\EventListener
 */
class KnpPagerItemsListener implements EventSubscriberInterface
{
    const KNP_PAGER_ITEMS_EVENT = 'knp_pager.items';

    /**
     * @var QueryBuilderManagerInterface
     */
    private $manager;

    public function __construct(QueryBuilderManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param ItemsEvent $event
     */
    public function onItems(ItemsEvent $event)
    {
        if (!$event->target instanceof QueryBuilder) {
            return;
        }
        try {
            $this->manager->apply($event->target);
        } catch (\Throwable $exception) {
            $message = "Bad filter request.";
            if (getenv('APP_ENV') === 'dev'){
                dump($exception);
                $message.=" Caused by: ".$exception->getMessage();
            }
            throw new BadRequestHttpException($message);
        }

    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            self::KNP_PAGER_ITEMS_EVENT => ['onItems', 10],
        );
    }
}

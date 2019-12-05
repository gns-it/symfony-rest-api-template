<?php
/**
 *  * Created by PhpStorm.
 * User: sergey_h
 * Date: 22.11.18
 * Time: 13:59
 */

namespace App\Controller;

use App\Entity\Extra\Groups;
use App\Entity\User\User;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Gns\GnsFilterBundle\Filtration\QueryHandlerStrategy\Impl\Serializer\ExcludeFieldsListExclusionStrategy;
use Gns\GnsFilterBundle\Filtration\QueryHandlerStrategy\Impl\Serializer\IncludeFieldsListExclusionStrategy;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SuperController
 * @package App\Controller
 * @method User|null getUser()
 */
class SuperController extends AbstractFOSRestController
{
    /**
     * @var IncludeFieldsListExclusionStrategy
     */
    private $includeStrategy;
    /**
     * @var ExcludeFieldsListExclusionStrategy
     */
    private $excludeStrategy;

    /**
     * @required
     * @param IncludeFieldsListExclusionStrategy $strategy
     */
    public function setIncludeExclusionStrategy(IncludeFieldsListExclusionStrategy $strategy): void
    {
        $this->includeStrategy = $strategy;
    }

    /**
     * @required
     * @param ExcludeFieldsListExclusionStrategy $strategy
     */
    public function setExcludeExclusionStrategy(ExcludeFieldsListExclusionStrategy $strategy): void
    {
        $this->excludeStrategy = $strategy;
    }

    /**
     * @param $data
     * @param array|string|null $groups
     * @param array $headers
     * @param int $statusCode
     * @return Response
     */
    public function response($data = null, $groups = null, array $headers = [], int $statusCode = 200): Response
    {
        if (null === $groups) {
            $groups = Groups::SHORT;
        }
        if (is_string($groups)) {
            $groups = [$groups];
        }
        $data = [
            'code' => $statusCode,
            'message' => 'OK',
            'payload' => $data,
        ];
        $view = $this->view($data, $statusCode, $headers);
        $context = new Context();
        $context->addExclusionStrategy($this->includeStrategy);
        $context->addExclusionStrategy($this->excludeStrategy);
        $view->setContext($context);
        if ($groups && count($groups)) {
            $context->setGroups($groups);
        }

        return $this->handleView($view);
    }

    /**
     * @return Response
     */
    public function okResponse(): Response
    {
        return $this->response('OK');
    }

}
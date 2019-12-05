<?= "<?php\n" ?>

namespace <?= $namespace ?>;

use <?= $entity_full_class_name ?>;
use <?= $form_full_class_name ?>;
<?php if (isset($repository_full_class_name)): ?>
use <?= $repository_full_class_name ?>;
<?php endif ?>
use App\Controller\SuperController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Entity\Extra\Groups;
use App\Form\FormHandler;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @Route("<?= $route_path ?>")
 * @SWG\Tag(name="<?= $entity_class_name ?>")
 */
class <?= $class_name ?> extends SuperController
{
    /**
     * @Route("", methods={"GET"})
     *
     * @SWG\Get(summary="Get list",
     *     @SWG\Response(
     *          response=Response::HTTP_OK,
     *          description="OK",
     *          @SWG\Schema(
     *              type="array",
     *              @SWG\Items(ref=@Model(type=<?= $entity_class_name ?>::class, groups={Groups::SHORT}))
     *          )
     *     ),
     *     @SWG\Parameter(
     *          name="pagination[limit]",
     *          in="query",
     *          type="string",
     *          required=false
     *     ),
     *     @SWG\Parameter(
     *          name="pagination[page]",
     *          in="query",
     *          type="string",
     *          required=false
     *     ),
     *     @SWG\Parameter(
     *          name="filter[name]",
     *          in="query",
     *          type="string",
     *          required=false
     *     ),
     *     @SWG\Parameter(
     *          name="order[name]",
     *          in="query",
     *          type="string",
     *          required=false,
     *          enum={"ASC","DESC"}
     *     )
     * )
     *
<?php if (isset($repository_class_name)): ?>
     * @param <?= $repository_class_name ?> $repository
     * @param Request $request
<?php endif ?>
     * @return Response
     */
<?php if (isset($repository_class_name)): ?>
    public function getList(Request $request, <?= $repository_class_name ?> $repository)
    {
        return $this->response($repository->findByParams($request->query->all()), Groups::SHORT);
    }
<?php else: ?>
    public function getCollection(): Response
    {
        $<?= $entity_var_plural ?> = $this->getDoctrine()->getRepository(<?= $entity_class_name ?>::class)->findAll();

        return $this->response( $<?= $entity_var_plural ?>, Groups::SHORT);
    }
<?php endif ?>

    /**
     * @Route("/{uuid}", methods={"GET"})
     *
     * @SWG\Get(summary="Get one",
     *     @SWG\Response(
     *          response=Response::HTTP_OK,
     *          description="OK",
     *          @Model(type=<?= $entity_class_name ?>::class, groups=Groups::DETAILED_SHORT)
     *     )
     * )
     *
     * @param <?= $entity_class_name ?> $<?= $entity_var_singular ?>

     * @return Response
    */
    public function getOne(<?= $entity_class_name ?> $<?= $entity_var_singular ?>)
    {
        return $this->response($<?= $entity_var_singular ?>, Groups::DETAILED_SHORT);
    }

    /**
     * @Route("", methods={"POST"})
     *
     * @SWG\Post(summary="Create",
     *     @SWG\Response(
     *          response=Response::HTTP_OK,
     *          description="OK"
     *     ),
     *     @SWG\Response(
     *          response=400,
     *          description="Validation error"
     *     ),
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          type="string",
     *          required=true,
     *          @Model(type=<?= $form_class_name ?>::class)
     *     )
     * )
     *
     * @param Request $request
     * @param FormHandler $formHandler
     * @return Response
     */
    public function post(Request $request, FormHandler $formHandler)
    {
        $formHandler->handler($request->request->all(), new <?= $entity_class_name ?>, <?= $form_class_name ?>::class);

        return $this->okResponse();
    }

    /**
     * @Route("/{uuid}", methods={"PUT"})
     *
     * @SWG\Put(summary="Update",
     *     @SWG\Response(
     *          response=Response::HTTP_OK,
     *          description="OK"
     *     ),
     *     @SWG\Response(
     *          response=400,
     *          description="Validation error"
     *     ),
     *     @SWG\Parameter(
     *          name="uuid",
     *          in="path",
     *          type="string",
     *          required=true,
     *          description="<?= $entity_class_name ?> uuid"
     *     ),
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          type="string",
     *          required=true,
     *          @Model(type=<?= $form_class_name ?>::class)
     *     )
     * )
     *
     * @param Request $request
     * @param FormHandler $formHandler
     * @param <?= $entity_class_name ?> $<?= $entity_var_singular ?>

     * @return Response
     */
    public function put(Request $request, FormHandler $formHandler, <?= $entity_class_name ?> $<?= $entity_var_singular ?>)
    {
        $formHandler->handler($request->request->all(), $<?= $entity_var_singular ?>, <?= $form_class_name ?>::class);

        return $this->okResponse();
    }

    /**
     * @Route("/{uuid}", methods={"DELETE"})
     *
     * @SWG\Delete(summary="Delete",
     *     @SWG\Response(
     *          response=Response::HTTP_NO_CONTENT,
     *          description="OK"
     *     ),
     *     @SWG\Parameter(
     *          name="uuid",
     *          in="path",
     *          type="string",
     *          required=true,
     *          description="<?= $entity_class_name ?> uuid"
     *     )
     * )
     *
     * @param EntityManagerInterface $em
     * @param <?= $entity_class_name ?> $<?= $entity_var_singular ?>

     * @return Response
     */
    public function delete(EntityManagerInterface $em, <?= $entity_class_name ?> $<?= $entity_var_singular ?>)
    {
        $em->remove($<?= $entity_var_singular ?>);
        $em->flush();

        return $this->okResponse();
    }
}
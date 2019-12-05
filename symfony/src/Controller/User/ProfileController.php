<?php
/**
 * @author Sergey Hashimov
 */

namespace App\Controller\User;

use App\Controller\SuperController;
use App\Entity\Extra\Groups;
use App\Entity\User\User;
use App\Form\FormHandler;
use App\Form\User\Profile\ChangePassType;
use App\Form\User\Profile\DeleteProfileType;
use App\Form\User\Profile\ProfileType;
use App\Repository\User\UserRepository;
use App\Service\User\Manager\UserManagerInterface;
use App\Service\User\ProfileDestructor;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile")
 * @SWG\Tag(name="User Profile")
 */
class ProfileController extends SuperController
{
    /**
     * @Route("/search", methods={"GET"})
     * @SWG\Get(summary="User search",
     *     @SWG\Response(
     *          response=Response::HTTP_OK,
     *          description="OK",
     *          @SWG\Schema(
     *              type="array",
     *              @SWG\Items(ref=@Model(type=User::class, groups={Groups::SHORT}))
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
     *          name="filter[name][value]",
     *          in="query",
     *          type="string",
     *          required=false
     *     ),
     *     @SWG\Parameter(
     *          name="filter[name][operator]",
     *          in="query",
     *          type="string",
     *          required=false
     *     ),
     *     @SWG\Parameter(
     *          name="filter[email][value]",
     *          in="query",
     *          type="string",
     *          required=false
     *     ),
     *     @SWG\Parameter(
     *          name="filter[email][operator]",
     *          in="query",
     *          type="string",
     *          required=false
     *     ),
     * )
     * @param UserRepository $repository
     * @param Request $request
     * @return Response
     */
    public function getList(Request $request, UserRepository $repository): Response
    {
        return $this->response($repository->findByParams($request->query->all()), Groups::SHORT);
    }

    /**
     * Reset user password.
     * @Route("/", methods={"GET"})
     * @SWG\Get(summary="Get profile",
     *
     *      @SWG\Response(
     *          response=200,
     *          description="OK",
     *                  @Model(type=User::class, groups=Groups::PROFILE_DETAILED)
     *      ),
     *      @SWG\Response(
     *          response=500,
     *          description="Server error",
     *          ),
     * )
     * @return Response
     */
    public function getProfile(): Response
    {
        return $this->response($this->getUser(), Groups::PROFILE_DETAILED);
    }

    /**
     * Reset user password.
     * @Route("/", methods={"PATCH"})
     * @SWG\Patch(summary="Edit profile",
     *
     *       @SWG\Response(
     *          response=200,
     *          description="OK",
     *                  @Model(type=User::class, groups=Groups::PROFILE_DETAILED)
     *      ),
     *      @SWG\Response(
     *          response=500,
     *          description="Server error",
     *          ),
     *      @SWG\Response(
     *          response=400,
     *          description="Validation error",
     *          ),
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          type="string",
     *          required=true,
     *          @Model(type=BusinessProfileType::class),
     *      ),
     * )
     * @param Request $request
     * @param FormHandler $formHandler
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function editProfile(Request $request, FormHandler $formHandler, EntityManagerInterface $em): Response
    {
        $type = ProfileType::class;
        $formHandler->handler($request->request->all(), $this->getUser(), $type, false);
        if (!$this->getUser()->isProfileFilled()) {
            $this->getUser()->addRole(User::ROLE_PROFILE_FILLED);
        }
        $em->flush();

        return $this->response($this->getUser(), Groups::PROFILE_DETAILED);
    }

    /**
     * Reset user password.
     * @Route("/change-password", methods={"POST"})
     * @SWG\Post(summary="Change password",
     *
     *       @SWG\Response(
     *          response=200,
     *          description="OK",
     *      ),
     *      @SWG\Response(
     *          response=500,
     *          description="Server error",
     *          ),
     *      @SWG\Response(
     *          response=400,
     *          description="Validation error",
     *          ),
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          type="string",
     *          required=true,
     *          @Model(type=ChangePassType::class)
     *      ),
     * )
     * @param Request $request
     * @param FormHandler $formHandler
     * @param UserManagerInterface $userManager
     * @return Response
     */
    public function changePassword(
        Request $request,
        FormHandler $formHandler,
        UserManagerInterface $userManager
    ): Response {
        $formHandler->handler($request->request->all(), $this->getUser(), ChangePassType::class, false);
        $userManager->updateUser($this->getUser());

        return $this->okResponse();
    }

    /**
     * Reset user password.
     * @Route("/", methods={"DELETE"})
     * @SWG\Delete(summary="Delete profile",
     *
     *       @SWG\Response(
     *          response=200,
     *          description="OK",
     *      ),
     *      @SWG\Response(
     *          response=500,
     *          description="Server error",
     *          ),
     *      @SWG\Response(
     *          response=400,
     *          description="Validation error",
     *          ),
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          type="string",
     *          required=true,
     *          @Model(type=DeleteProfileType::class)
     *      ),
     * )
     * @param Request $request
     * @param FormHandler $formHandler
     * @param ProfileDestructor $destructor
     * @return Response
     * @throws \Exception
     */
    public function deleteAccount(Request $request, FormHandler $formHandler, ProfileDestructor $destructor): Response
    {
        $formHandler->handler($request->request->all(), null, DeleteProfileType::class, false);
        $destructor->run($this->getUser(), true);

        return $this->okResponse();
    }


}


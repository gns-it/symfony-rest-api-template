<?php

namespace App\Controller\File;

use App\Controller\SuperController;
use App\Entity\Extra\Groups;
use App\Entity\Media\Media;
use App\Service\Media\MediaManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;

/**
 * @Route("file")
 * @SWG\Tag(name="File")
 */
class FileController extends SuperController
{
    /**
     * @Route("", methods={"POST"})
     *
     * @SWG\Post(summary="Create",
     *     @SWG\Response(
     *          response=Response::HTTP_OK,
     *          description="OK",
     *          @Model(type=Media::class, groups={Groups::SHORT})
     *     ),
     *     @SWG\Parameter(
     *          name="file",
     *          in="formData",
     *          type="file",
     *          required=true,
     *          description="Uploaded file"
     *     )
     * )
     *
     * @param Request $request
     * @param MediaManager $mediaManager
     * @return Response
     */
    public function upload(Request $request, MediaManager $mediaManager)
    {
        $uploadedFile = $request->files->get('file');

        if (null === $uploadedFile) {
            throw new BadRequestHttpException('File does not exist');
        }

       $media = $mediaManager->createFromUploadedFile($uploadedFile);

        return $this->response($media, Groups::SHORT);
    }
}
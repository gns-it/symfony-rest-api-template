<?php

namespace App\Controller\Admin\Activity;

use App\Controller\SuperController;
use App\Repository\Activity\LogRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/activity/log",name="admin_activity_log_")
 */
class LogController extends SuperController
{

    /**
     * @Route("", methods={"GET"}, name="index", options={"search"={"Activgity log list"={}}})
     * @param Request $request
     * @param LogRepository $repository
     * @return Response
     */
    public function index(Request $request, LogRepository $repository)
    {
        return $this->render(
            'Admin/ActivityLog/index.html.twig',
            [
                'entities' => $repository->htmlPaginate($request),
            ]
        );
    }

}
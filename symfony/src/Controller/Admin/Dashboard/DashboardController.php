<?php

namespace App\Controller\Admin\Dashboard;

use App\Controller\SuperController;
use App\Service\Admin\SearchEntriesProvider;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class DashboardController extends SuperController
{

    /**
     * @Route("", methods={"GET"}, name="admin_dashboard", options={"search"={"Dashboard"={}}})
     * @return Response
     */
    public function index()
    {
        return $this->render('Admin/base.html.twig');
    }

    /**
     * @Route("/search/{text}", methods={"GET"}, name="search", condition="request.isXmlHttpRequest()")
     * @param string $text
     * @param SearchEntriesProvider $provider
     * @return Response
     */
    public function search(string $text, SearchEntriesProvider $provider)
    {
        return $this->response($provider->search($text));
    }

}
<?php

namespace App\Controller\OAuth;

use App\Controller\SuperController;
use App\Service\OAuth\OAuth2;
use FOS\RestBundle\Controller\Annotations as Rest;
use OAuth2\OAuth2ServerException;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Rest\Route("/oauth")
 */
class TokenController extends SuperController
{

    /**
     * @Route("/token", methods={"POST"}, name="app_get_token")
     * @SWG\Post(summary="Authorize user",
     *
    description="
    |------------------------------------------------Facebook-----------------------------------------------------|
    |{                                                                                                            |
    |   'client_id': '2_ha6j7fr7q7coowcc4sck84ccgoo4ook8o4cg0k8sgsook8kgg',                                       |
    |   'client_secret': '4mz7i9a7za0ws0k0ow0ss4s0ggo80ksck8k40ooowkokos48ck',                                    |
    |   'grant_type': 'http://facebook.com',                                                                      |
    |   'remote_token': 'dsaddr5yghgnae;o943u5io34u9',                                                            |
    |   'email': 'custom@email.com',                                                                              |
    | }                                                                                                           |
    |-------------------------------------------------------------------------------------------------------------|
    |                                                 Google                                                      |
    |-------------------------------------------------------------------------------------------------------------|
    |{                                                                                                            |
    |   'client_id': '2_ha6j7fr7q7coowcc4sck84ccgoo4ook8o4cg0k8sgsook8kgg',                                       |
    |   'client_secret': '4mz7i9a7za0ws0k0ow0ss4s0ggo80ksck8k40ooowkokos48ck',                                    |
    |   'grant_type': 'http://google.com',                                                                        |
    |   'remote_token': 'dsaddr5yghgnae;o943u5io34u9',                                                            |
    |   'email': 'custom@email.com',                                                                              |
    | }                                                                                                           |
    |-------------------------------------------------------------------------------------------------------------|
    |                                              Refresh token                                                  |
    |-------------------------------------------------------------------------------------------------------------|
    |{                                                                                                            |
    |   'refresh_token': 'ZmYwYTVmMDE2OWE2M2UzZmZkZGE1YWVkMDc0MzM2YTlkMjEwOTM5OWJkN2ZhOTgzYjI4NWM0N2Q3MTVkYTEzMQ' |
    |   'client_id': '2_ha6j7fr7q7coowcc4sck84ccgoo4ook8o4cg0k8sgsook8kgg',                                       |
    |   'client_secret': '4mz7i9a7za0ws0k0ow0ss4s0ggo80ksck8k40ooowkokos48ck',                                    |
    |   'grant_type': 'refresh_token',                                                                            |
    | }                                                                                                           |
    |-------------------------------------------------------------------------------------------------------------|
    |                                              User credentials                                               |
    |-------------------------------------------------------------------------------------------------------------|
    |{                                                                                                            |
    |   'refresh_token': 'ZmYwYTVmMDE2OWE2M2UzZmZkZGE1YWVkMDc0MzM2YTlkMjEwOTM5OWJkN2ZhOTgzYjI4NWM0N2Q3MTVkYTEzMQ' |
    |   'client_id': '2_ha6j7fr7q7coowcc4sck84ccgoo4ook8o4cg0k8sgsook8kgg',                                       |
    |   'client_secret': '4mz7i9a7za0ws0k0ow0ss4s0ggo80ksck8k40ooowkokos48ck',                                    |
    |   'grant_type': 'password',                                                                                 |
    | }                                                                                                           |
    |-------------------------------------------------------------------------------------------------------------|
    ",
     *
     *      @SWG\Response(
     *          response=200,
     *          description="OK",
     *          @SWG\Schema(
     *              type="object",
     *              	@SWG\Property(property="access_token", type="string", example="ZDY1YTUyYzA2MjMyODIwMTY2M2JhMmZmN2Q2NGVmZjFjOTE0NDE4OTQxNTI0MWZiMGFhZWE2M2ZkNGZkNDNkYg"),
     *              	@SWG\Property(property="expires_in", type="string", example="3600"),
     *              	@SWG\Property(property="token_type", type="string", example="bearer"),
     *              	@SWG\Property(property="scope", type="string", example="null"),
     *              	@SWG\Property(property="refresh_token", type="string", example="YWQxMzI0MjVkMDc4YTA3NGNmM2ExNDgyZTY0MjkxMzVhZTk4MWI1OGQ1MjM0ZDI0MWM3YWZmOTQ2MWFiMjhiYQ"),
     *          )
     *      ),
     *      @SWG\Response(
     *          response=400,
     *          description="
     *          [invalid_client] - The client credentials are invalid,
     *          [grant_type_is_missing_or_invalid] - Invalid grant_type parameter or parameter missing,
     *          [invalid_user_credentials] - Invalid username and password combination,
     *          [required_parameter_is_missing] - Pass or username missing"
     *      ),
     *      @SWG\Response(
     *          response=406,
     *          description="User account not confirmed"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          type="string",
     *          required=true,
     *          @SWG\Definition(
     *              required={"client_id", "client_secret", "grant_type", "username", "password"},
     *              @SWG\Property(property="refresh_token", example="ZmYwYTVmMDE2OWE2M2UzZmZkZGE1YWVkMDc0MzM2YTlkMjEwOTM5OWJkN2ZhOTgzYjI4NWM0N2Q3MTVkYTEzMQ", type="string", description="токен восстановления"),
     *              @SWG\Property(property="client_id", example="2_ha6j7fr7q7coowcc4sck84ccgoo4ook8o4cg0k8sgsook8kgg", type="string", description="ID клиента"),
     *              @SWG\Property(property="client_secret", example="4mz7i9a7za0ws0k0ow0ss4s0ggo80ksck8k40ooowkokos48ck", type="string", description="Секретный ключ клиента"),
     *              @SWG\Property(property="grant_type", format="password, refresh_token, 'http://google.com', 'http://facebook.com'", example="password", type="string", description="Тип доступа"),
     *              @SWG\Property(property="username", example="fake.user@email.com", type="string", description="Email пользователя. Обязательный для доступа по: password"),
     *              @SWG\Property(property="password", example="fakeuser", type="string", description="Пароль пользователя. Обязательный для доступа по : password"),
     *          )
     *      ),
     * )
     * @SWG\Tag(name="User Authorization")
     * @param OAuth2 $server
     * @param Request $request
     * @throws OAuth2ServerException
     * @return Response
     */
    public function tokenAction(OAuth2 $server, Request $request): Response
    {
        return $server->grantAccessToken($request);
    }
}
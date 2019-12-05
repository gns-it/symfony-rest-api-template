<?php


namespace App\Service\OAuth;

use App\Event\User\BeforeTokenGenerateEvent;
use OAuth2\IOAuth2GrantUser;
use OAuth2\IOAuth2Storage;
use OAuth2\Model\IOAuth2AccessToken;
use OAuth2\Model\IOAuth2Client;
use OAuth2\OAuth2 as BaseOAuth2;
use OAuth2\OAuth2AuthenticateException;
use OAuth2\OAuth2ServerException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class OAuth2
 * @package App\Service\OAuth
 */
class OAuth2 extends BaseOAuth2
{
    const GRANT_TYPE_IS_MISSING_OR_INVALID = 'grant_type_is_missing_or_invalid';

    const INVALID_USER_CREDENTIALS = 'invalid_user_credentials';

    const USER_NOT_CONFIRMED = 'user_not_confirmed';

    const USER_DELETED = 'user_deleted';

    const USER_BLOCKED = 'user_blocked';

    const REQUIRED_PARAMETER_IS_MISSING = 'required_parameter_is_missing';

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(IOAuth2Storage $storage, array $config = array(), EventDispatcherInterface $dispatcher)
    {
        parent::__construct($storage, $config);
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param string $tokenParam
     * @param null $scope
     * @return IOAuth2AccessToken
     * @throws OAuth2AuthenticateException
     */
    public function verifyAccessToken($tokenParam, $scope = null)
    {
        $tokenType = $this->getVariable(self::CONFIG_TOKEN_TYPE);
        $realm = $this->getVariable(self::CONFIG_WWW_REALM);

        if (!$tokenParam) {
            throw new OAuth2AuthenticateException(
                Response::HTTP_BAD_REQUEST,
                $tokenType,
                $realm,
                self::ERROR_INVALID_REQUEST,
                'The request is missing a required parameter, includes an unsupported parameter or parameter value, repeats the same parameter, uses more than one method for including an access token, or is otherwise malformed.',
                $scope
            );
        }

        $token = $this->storage->getAccessToken($tokenParam);
        if (!$token) {
            throw new OAuth2AuthenticateException(
                Response::HTTP_UNAUTHORIZED,
                $tokenType,
                $realm,
                self::ERROR_INVALID_GRANT,
                'The access token provided is invalid.',
                $scope
            );
        }

        if ($token->hasExpired()) {
            throw new OAuth2AuthenticateException(
                Response::HTTP_UNAUTHORIZED,
                $tokenType,
                $realm,
                self::ERROR_INVALID_GRANT,
                'The access token provided has expired.',
                $scope
            );
        }

        if ($scope && (!$token->getScope() || !$this->checkScope($scope, $token->getScope()))) {
            throw new OAuth2AuthenticateException(
                Response::HTTP_FORBIDDEN,
                $tokenType,
                $realm,
                self::ERROR_INSUFFICIENT_SCOPE,
                'The request requires higher privileges than provided by the access token.',
                $scope
            );
        }

        return $token;
    }

    /**
     * @param Request|null $request
     * @return Response
     * @throws OAuth2ServerException
     */
    public function grantAccessToken(Request $request = null)
    {
        $filters = array(
            'grant_type' => array(
                'filter' => FILTER_VALIDATE_REGEXP,
                'options' => array('regexp' => self::GRANT_TYPE_REGEXP),
                'flags' => FILTER_REQUIRE_SCALAR,
            ),
            'scope' => array('flags' => FILTER_REQUIRE_SCALAR),
            'code' => array('flags' => FILTER_REQUIRE_SCALAR),
            'redirect_uri' => array('filter' => FILTER_SANITIZE_URL),
            'username' => array('flags' => FILTER_REQUIRE_SCALAR),
            'password' => array('flags' => FILTER_REQUIRE_SCALAR),
            'refresh_token' => array('flags' => FILTER_REQUIRE_SCALAR),
        );

        if ($request === null) {
            $request = Request::createFromGlobals();
        }

        if ($request->getMethod() === 'POST') {
            $inputData = $request->request->all();
        } else {
            $inputData = $request->query->all();
        }

        $authHeaders = $this->getAuthorizationHeader($request);

        $input = filter_var_array($inputData, $filters);

        if (!$input['grant_type']) {
            throw new OAuth2ServerException(
                Response::HTTP_BAD_REQUEST,
                self::GRANT_TYPE_IS_MISSING_OR_INVALID,
                'Invalid grant_type parameter or parameter missing'
            );
        }

        $clientCredentials = $this->getClientCredentials($inputData, $authHeaders);

        $client = $this->storage->getClient($clientCredentials[0]);

        if (!$client) {
            throw new OAuth2ServerException(
                Response::HTTP_BAD_REQUEST,
                self::ERROR_INVALID_CLIENT,
                'The client credentials are invalid'
            );
        }

        if ($this->storage->checkClientCredentials($client, $clientCredentials[1]) === false) {
            throw new OAuth2ServerException(
                Response::HTTP_BAD_REQUEST,
                self::ERROR_INVALID_CLIENT,
                'The client credentials are invalid'
            );
        }

        if (!$this->storage->checkRestrictedGrantType($client, $input['grant_type'])) {
            throw new OAuth2ServerException(
                Response::HTTP_BAD_REQUEST,
                self::ERROR_UNAUTHORIZED_CLIENT,
                'The grant type is unauthorized for this client_id'
            );
        }

        switch ($input['grant_type']) {
            case self::GRANT_TYPE_AUTH_CODE:
                $stored = $this->grantAccessTokenAuthCode($client, $input);
                break;
            case self::GRANT_TYPE_USER_CREDENTIALS:
                $stored = $this->grantAccessTokenUserCredentials($client, $input);
                break;
            case self::GRANT_TYPE_CLIENT_CREDENTIALS:
                $stored = $this->grantAccessTokenClientCredentials($client, $input, $clientCredentials);
                break;
            case self::GRANT_TYPE_REFRESH_TOKEN:
                $stored = $this->grantAccessTokenRefreshToken($client, $input);
                break;
            default:
                if (substr($input['grant_type'], 0, 4) !== 'urn:'
                    && !filter_var($input['grant_type'], FILTER_VALIDATE_URL)
                ) {
                    throw new OAuth2ServerException(
                        Response::HTTP_BAD_REQUEST,
                        self::ERROR_INVALID_REQUEST,
                        'Invalid grant_type parameter or parameter missing'
                    );
                }

                $stored = $this->grantAccessTokenExtension($client, $inputData, $authHeaders);
        }

        if (!is_array($stored)) {
            $stored = array();
        }

        $stored += array(
            'scope' => $this->getVariable(self::CONFIG_SUPPORTED_SCOPES, null),
            'data' => null,
            'access_token_lifetime' => $this->getVariable(self::CONFIG_ACCESS_LIFETIME),
            'issue_refresh_token' => true,
            'refresh_token_lifetime' => $this->getVariable(self::CONFIG_REFRESH_LIFETIME),
        );

        $scope = $stored['scope'];
        if ($input['scope']) {
            if (!isset($stored['scope']) || !$this->checkScope($input['scope'], $stored['scope'])) {
                throw new OAuth2ServerException(
                    Response::HTTP_BAD_REQUEST,
                    self::ERROR_INVALID_SCOPE,
                    'An unsupported scope was requested.'
                );
            }
            $scope = $input['scope'];
        }
        if ($stored['data']->isBlocked()) {
            throw new OAuth2ServerException(
                Response::HTTP_NOT_ACCEPTABLE,
                self::USER_BLOCKED,
                "User's account blocked"
            );
        }
        $this->dispatcher->dispatch(new BeforeTokenGenerateEvent($stored['data'], $request));

        $token = $this->createAccessToken(
            $client,
            $stored['data'],
            $scope,
            $stored['access_token_lifetime'],
            $stored['issue_refresh_token'],
            $stored['refresh_token_lifetime']
        );

        return new Response(json_encode($token), 200, $this->getJsonHeaders());
    }

    /**
     * @param IOAuth2Client $client
     * @param array $input
     *
     * @return array|bool
     * @throws OAuth2ServerException
     */
    protected function grantAccessTokenUserCredentials(IOAuth2Client $client, array $input)
    {
        if (!($this->storage instanceof IOAuth2GrantUser)) {
            throw new OAuth2ServerException(Response::HTTP_BAD_REQUEST, self::ERROR_UNSUPPORTED_GRANT_TYPE);
        }

        if (!$input['username'] || !$input['password']) {
            throw new OAuth2ServerException(
                Response::HTTP_BAD_REQUEST,
                self::REQUIRED_PARAMETER_IS_MISSING,
                'Missing parameters. "username" and "password" required'
            );
        }

        $stored = $this->storage->checkUserCredentials($client, $input['username'], $input['password']);
        if ($stored === false) {
            throw new OAuth2ServerException(
                Response::HTTP_BAD_REQUEST,
                self::INVALID_USER_CREDENTIALS,
                'Invalid username and password combination'
            );
        }
//        if (!$stored['data']->isEnabled() && $stored['data']->getConfirmationToken()) {
//            throw new OAuth2ServerException(
//                Response::HTTP_FORBIDDEN,
//                self::USER_NOT_CONFIRMED,
//                "User's account not confirmed"
//            );
//        }
//        if (!$stored['data']->isEnabled() && null === $stored['data']->getConfirmationToken()) {
//            throw new OAuth2ServerException(
//                Response::HTTP_NOT_ACCEPTABLE,
//                self::USER_BLOCKED,
//                "User's account blocked"
//            );
//        }

        return $stored;
    }

    /**
     * Returns HTTP headers for JSON.
     *
     * @see     http://tools.ietf.org/html/draft-ietf-oauth-v2-20#section-5.1
     * @see     http://tools.ietf.org/html/draft-ietf-oauth-v2-20#section-5.2
     *
     * @return array
     *
     * @ingroup oauth2_section_5
     */
    private function getJsonHeaders()
    {
        $headers = $this->getVariable(self::CONFIG_RESPONSE_EXTRA_HEADERS, array());
        $headers += array(
            'Content-Type' => 'application/json',
            'Cache-Control' => 'no-store',
            'Pragma' => 'no-cache',
        );

        return $headers;
    }
}
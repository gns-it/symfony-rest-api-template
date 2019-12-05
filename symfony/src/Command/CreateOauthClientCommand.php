<?php

namespace App\Command;

use App\Service\Security\GrantExtension\FacebookGrantExtension;
use App\Service\Security\GrantExtension\GoogleGrantExtension;
use FOS\OAuthServerBundle\Model\ClientManagerInterface;
use OAuth2\OAuth2;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateOauthClientCommand extends Command
{
    protected static $defaultName = 'app:create-oauth-client';
    /**
     * @var ClientManagerInterface
     */
    private $clientManager;

    /**
     * CreateOauthClientCommand constructor.
     * @param null $name
     * @param ClientManagerInterface $clientManager
     */
    public function __construct($name = null, ClientManagerInterface $clientManager)
    {
        parent::__construct(self::$defaultName);

        $this->clientManager = $clientManager;
    }

    /**
     *
     */
    protected function configure()
    {
        $this->setName(self::$defaultName)
            ->setDescription('Add a short description for your command');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $client = $this->clientManager->createClient();
        $client->setRedirectUris(['http://www.example.com']);
        $client->setAllowedGrantTypes(
            [
                OAuth2::GRANT_TYPE_USER_CREDENTIALS,
                OAuth2::RESPONSE_TYPE_ACCESS_TOKEN,
                OAuth2::GRANT_TYPE_AUTH_CODE,
                OAuth2::GRANT_TYPE_REFRESH_TOKEN,
                GoogleGrantExtension::URI,
                FacebookGrantExtension::URI,
            ]
        );
        $this->clientManager->updateClient($client);

        $io->success(
            "\nNew OAuth client entity was generated\nClient Id: {$client->getPublicId()}\nClient Secret: {$client->getSecret()}"
        );
    }
}

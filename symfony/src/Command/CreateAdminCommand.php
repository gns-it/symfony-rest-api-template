<?php

namespace App\Command;

use App\Entity\User\User;
use App\Service\User\Manager\UserManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateAdminCommand extends Command
{
    protected static $defaultName = 'app:create-admin';
    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * CreateOauthClientCommand constructor.
     * @param null $name
     * @param UserManagerInterface $userManager
     */
    public function __construct($name = null, UserManagerInterface $userManager)
    {
        parent::__construct(self::$defaultName);
        $this->userManager = $userManager;
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
        /** @var User $user */
        $user = $this->userManager->createUser();
        $user->setEmail('admin@admin.com');
        $user->setPlainPassword('Qwerty123');
        $user->setRoles(['ROLE_SUPER_ADMIN']);
        $user->enable();
        $this->userManager->updateUser($user);
    }
}

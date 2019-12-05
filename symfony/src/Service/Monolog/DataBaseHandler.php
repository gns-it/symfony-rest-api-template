<?php
/**
 * @author Sergey Hashimov <hashimov.sergey@gmail.com>
 */

namespace App\Service\Monolog;

use App\Entity\Activity\Log;
use App\Entity\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Handler\AbstractProcessingHandler;
use Symfony\Component\Security\Core\Security;

class DataBaseHandler extends AbstractProcessingHandler
{

    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var Security
     */
    private $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        parent::__construct();
        $this->em = $em;
        $this->security = $security;
    }

    /**
     * Writes the record down to the log of the implementing handler
     * @param array $record
     * @return void
     */
    protected function write(array $record)
    {
        $logEntry = new Log();
        $logEntry->setMessage($record['message']);
        $logEntry->setLevel($record['level']);
        $logEntry->setLevelName($record['level_name']);
        $logEntry->setExtra($this->extra());
        $logEntry->setContext($record['context']);
        $this->em->persist($logEntry);
        $this->em->flush();
    }

    /**
     * @return array
     */
    private function extra()
    {
        $user = $this->security->getUser();
        if (!$user) {
            return [];
        }
        $userName = null;
        if ($user instanceof User) {
            return ['user' => "{$user->getName()} {$user->getEmail()} {$user->getProfileName()}"];
        }

        /** @var \Symfony\Component\Security\Core\User\User $user */
        return ['user' => "Machine: {$user->getUsername()} {$user->getRoles()[0]}"];

    }
}
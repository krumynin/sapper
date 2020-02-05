<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendEmailsCommand extends ContainerAwareCommand
{
    const NAME = 'app:send-emails';

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * SetFinishedAtCommand constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName(self::NAME)
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

//        $users = $this->em->getRepository(User::class)->findAll();
//        foreach ($users as $user){
//            $emails[] = $user->getEmail();
//        }

        $this->getContainer()->get('old_sound_rabbit_mq.send_email_producer')->publish('test');

//        $message = (new \Swift_Message('Hello Email'))
//            ->setFrom('send@example.com')
//            ->setTo('recipient@example.com')
//            ->setBody('TEST 123', 'text/html')
//        ;

        $output->writeln('goood');
    }

}

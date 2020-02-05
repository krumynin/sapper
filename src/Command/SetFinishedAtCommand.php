<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use App\Entity\Field;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetFinishedAtCommand extends Command
{
    const NAME = 'app:set-finishedAt';

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
            ->addArgument('id', InputArgument::REQUIRED, 'Field id')
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $fieldId = $input->getArgument('id');
        $field = $this->em->getRepository(Field::class)->find($fieldId);
        $date = new \DateTime();
        $field->setFinishedAt($date);

//        $em = $this->getDoctrine()->getManager();
        $this->em->persist($field);
        $this->em->flush();

        $output->writeln($fieldId);
    }
}
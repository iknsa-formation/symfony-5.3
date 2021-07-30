<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCreateAdminCommand extends Command
{
    protected static $defaultName = 'app:user:create-admin';
    protected static $defaultDescription = 'Add a short description for your command';

    private UserPasswordHasherInterface $passwordEncoder;
    private ObjectManager $manager;

    public function __construct(UserPasswordHasherInterface $passwordEncoder, ManagerRegistry $registry)
    {
        parent::__construct(self::$defaultName);
        $this->passwordEncoder = $passwordEncoder;
        $this->manager = $registry->getManager();
    }

    protected function configure(): void
    {
        $this
            ->setName(self::$defaultName)
            ->addArgument('email', InputArgument::REQUIRED, 'some Email')
            ->addArgument('password', InputArgument::REQUIRED, 'pass')
            ->addArgument('firstName', InputArgument::REQUIRED, 'firstName')
            ->addArgument('name', InputArgument::REQUIRED, 'name')
        ;
    }

    public function interact(InputInterface $input, OutputInterface $output)
    {
        $questions = $this->getHelper('question');

        $email = new Question('Your email :');
        $input->setArgument('email', $questions->ask($input, $output, $email));
        $password = new Question('Your password :');
        $input->setArgument('password', $questions->ask($input, $output, $password));
        $firstName = new Question('Your first name :');
        $input->setArgument('firstName', $questions->ask($input, $output, $firstName));
        $name = new Question('Your name :');
        $input->setArgument('name', $questions->ask($input, $output, $name));
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $firstName = $input->getArgument('firstName');
        $name = $input->getArgument('name');

        $user = new User();
        $user->setEmail($email)
        ->addRole('ROLE_ADMIN')
        ->setFirstName($firstName)
        ->setName($name);

        $user->setPassword($this->passwordEncoder->hashPassword(
            $user,
            $password
        ));

        $manager = $this->manager;
        $manager->persist($user);
        $manager->flush();

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}

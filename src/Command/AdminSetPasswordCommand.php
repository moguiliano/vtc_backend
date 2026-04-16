<?php

namespace App\Command;

use App\Entity\Admin;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:admin:set-password',
    description: 'Définir ou réinitialiser le mot de passe d\'un administrateur',
)]
class AdminSetPasswordCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $hasher,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('ZenCAR — Définir le mot de passe admin');

        $repo   = $this->em->getRepository(Admin::class);
        $admins = $repo->findAll();

        if (empty($admins)) {
            $io->error('Aucun compte admin trouvé en base de données.');
            return Command::FAILURE;
        }

        // Afficher la liste des admins
        $choices = [];
        foreach ($admins as $admin) {
            $choices[$admin->getEmail()] = sprintf('%s (%s)', $admin->getNom() ?? 'Sans nom', $admin->getEmail());
        }

        $email = $io->choice('Quel compte ?', array_values($choices));

        // Retrouver l'entité correspondante
        $selected = null;
        foreach ($admins as $admin) {
            $label = sprintf('%s (%s)', $admin->getNom() ?? 'Sans nom', $admin->getEmail());
            if ($label === $email) {
                $selected = $admin;
                break;
            }
        }

        if (!$selected) {
            $io->error('Compte introuvable.');
            return Command::FAILURE;
        }

        // Demander le nouveau mot de passe (masqué)
        $password = $io->askHidden('Nouveau mot de passe (min. 8 caractères)');
        if (!$password || strlen($password) < 8) {
            $io->error('Le mot de passe doit contenir au moins 8 caractères.');
            return Command::FAILURE;
        }

        $confirm = $io->askHidden('Confirmer le mot de passe');
        if ($password !== $confirm) {
            $io->error('Les mots de passe ne correspondent pas.');
            return Command::FAILURE;
        }

        $selected->setPassword($this->hasher->hashPassword($selected, $password));
        $this->em->flush();

        $io->success(sprintf('Mot de passe mis à jour pour %s', $selected->getEmail()));

        return Command::SUCCESS;
    }
}

<?php

namespace App\Service;

use App\Entity\CompteAbonne;
use App\Repository\CompteAbonneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Mots de passe des abonnés (D-108, D-109) : provisoire donné par le super-admin, changement par l'abonné,
 * « mot de passe oublié » par e-mail (lien 1 h, usage unique).
 */
class MotDePasseAbonne
{
    public const LONGUEUR_MIN = 8;

    public function __construct(
        private readonly UserPasswordHasherInterface $hasher,
        private readonly EntityManagerInterface $em,
        private readonly CompteAbonneRepository $comptes,
        private readonly MailerInterface $mailer,
        private readonly UrlGeneratorInterface $urls,
        private readonly LoggerInterface $logger,
        #[Autowire('%env(MAILER_FROM)%')] private readonly string $expediteur,
    ) {}

    /** Mot de passe provisoire lisible (sans 0/O, 1/l/I), 12 caractères ; l'ancien ne marche plus. */
    public function provisoire(CompteAbonne $compte): string
    {
        $alphabet = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789';
        $mdp = '';
        for ($i = 0; $i < 12; $i++) {
            $mdp .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }
        $this->changer($compte, $mdp);

        return $mdp;
    }

    public function changer(CompteAbonne $compte, string $nouveau): void
    {
        $compte->setPassword($this->hasher->hashPassword($compte, $nouveau));
        $compte->effacerLienReset();
        $this->em->flush();
    }

    public function motDePasseValide(CompteAbonne $compte, string $mdp): bool
    {
        return $this->hasher->isPasswordValid($compte, $mdp);
    }

    /**
     * « Mot de passe oublié » : si le compte existe, crée un lien et l'envoie. Ne dit jamais si le compte existe.
     * Si l'envoi échoue (adresse d'envoi pas encore configurée…), on le note dans le journal, sans rien dire.
     */
    public function envoyerLien(string $email): void
    {
        $compte = $this->comptes->findOneBy(['email' => mb_strtolower(trim($email))]);
        if (!$compte) {
            return;
        }

        $secret = $compte->nouveauLienReset(new \DateTimeImmutable());
        $this->em->flush();

        $lien = $this->urls->generate('espace_nouveau_mot_de_passe', ['secret' => $secret], UrlGeneratorInterface::ABSOLUTE_URL);
        try {
            $this->mailer->send((new TemplatedEmail())
                ->from(new Address($this->expediteur, 'ZenCAR Espace pro'))
                ->to($compte->getEmail())
                ->subject('Votre nouveau mot de passe ZenCAR')
                ->htmlTemplate('espace/email/reset.html.twig')
                ->textTemplate('espace/email/reset.txt.twig')
                ->context(['lien' => $lien, 'nom' => $compte->getEntreprise()?->getNom()]));
        } catch (\Throwable $e) {
            $this->logger->error('Mot de passe oublié : e-mail non envoyé ({message})', ['message' => $e->getMessage()]);
        }
    }

    /** Compte correspondant à un lien encore valable, sinon null. */
    public function compteDuLien(string $secret): ?CompteAbonne
    {
        if (!preg_match('/^[a-f0-9]{64}$/', $secret)) {
            return null;
        }
        $compte = $this->comptes->findOneBy(['resetHash' => hash('sha256', $secret)]);

        return $compte && $compte->lienResetValide($secret, new \DateTimeImmutable()) ? $compte : null;
    }
}

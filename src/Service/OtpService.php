<?php

namespace App\Service;

use App\Entity\VerificationCode;
use Doctrine\ORM\EntityManagerInterface;

class OtpService
{
    public function __construct(
        private EntityManagerInterface $em,
        private TwilioService $twilio
    ) {}

    /** Génère + enregistre + envoie. Retourne true/false. Ne jette pas. */
    public function sendOtp(string $phone): bool
    {
        try {
            $code = random_int(100000, 999999);

            $otp = (new VerificationCode())
                ->setPhoneNumber($phone)
                ->setCode((string)$code)
                ->setCreatedAt(new \DateTime())
                ->setIsVerified(false);

            $this->em->persist($otp);
            $this->em->flush();

            $msg = "ZenCAR : votre code de vérification est {$code}. (valide 5 min)";
            return $this->twilio->sendSms($phone, $msg);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /** Vérifie le code et marque vérifié si OK. Retourne true/false. */
    public function verifyCode(string $phone, string $code): bool
    {
        try {
            $repo = $this->em->getRepository(VerificationCode::class);
            $otp  = $repo->findOneBy([
                'phoneNumber' => $phone,
                'code'        => $code,
                'isVerified'  => false,
            ]);
            if (!$otp) {
                return false;
            }
            $otp->setIsVerified(true);
            $this->em->flush();
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }
}

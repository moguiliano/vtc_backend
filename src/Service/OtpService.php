<?php

namespace App\Service;

use App\Entity\VerificationCode;
use Doctrine\ORM\EntityManagerInterface;

class OtpService
{
    public function __construct(
        private EntityManagerInterface $em,
        private TwilioService $twilio,
        private int $ttlMinutes = 5,
        private int $antispamSeconds = 60,
        private int $maxAttempts = 5,
    ) {}

    /**
     * Génère, stocke et envoie un code OTP.
     * Retourne ['status' => 'sent'|'cooldown'|'error', 'cooldown' => N, 'expiresIn' => N]
     */
    public function sendOtp(string $phone): array
    {
        try {
            $repo = $this->em->getRepository(VerificationCode::class);
            $existing = $repo->findOneBy(['phoneNumber' => $phone, 'isVerified' => false], ['createdAt' => 'DESC']);

            if ($existing && $existing->getLastSentAt()) {
                $elapsed = time() - $existing->getLastSentAt()->getTimestamp();
                if ($elapsed < $this->antispamSeconds) {
                    return ['status' => 'cooldown', 'cooldown' => $this->antispamSeconds - $elapsed];
                }
            }

            $code      = (string) random_int(100000, 999999);
            $now       = new \DateTime();
            $expiresAt = (new \DateTime())->modify("+{$this->ttlMinutes} minutes");

            if ($existing) {
                $existing
                    ->setCode($code)
                    ->setCreatedAt($now)
                    ->setExpiresAt($expiresAt)
                    ->setAttempts(0)
                    ->setLastSentAt($now)
                    ->setIsVerified(false);
            } else {
                $otp = (new VerificationCode())
                    ->setPhoneNumber($phone)
                    ->setCode($code)
                    ->setCreatedAt($now)
                    ->setExpiresAt($expiresAt)
                    ->setAttempts(0)
                    ->setLastSentAt($now)
                    ->setIsVerified(false);
                $this->em->persist($otp);
            }

            $this->em->flush();

            $msg = "ZenCAR : votre code de vérification est {$code}. Ne le partagez pas. (valide {$this->ttlMinutes} min)";
            $this->twilio->sendSms($phone, $msg);

            return ['status' => 'sent', 'expiresIn' => $this->ttlMinutes * 60];
        } catch (\Throwable $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Vérifie un code OTP.
     * Retourne ['valid' => bool, 'error' => string|null, 'remaining' => int|null]
     */
    public function verifyCode(string $phone, string $code): array
    {
        try {
            $repo = $this->em->getRepository(VerificationCode::class);
            $otp  = $repo->findOneBy(['phoneNumber' => $phone, 'isVerified' => false], ['createdAt' => 'DESC']);

            if (!$otp || !$otp->getExpiresAt()) {
                return ['valid' => false, 'error' => 'NO_CODE'];
            }

            if (new \DateTime() > $otp->getExpiresAt()) {
                return ['valid' => false, 'error' => 'EXPIRED'];
            }

            if ($otp->getAttempts() >= $this->maxAttempts) {
                return ['valid' => false, 'error' => 'TOO_MANY_ATTEMPTS'];
            }

            if (hash_equals($otp->getCode(), $code)) {
                $otp->setIsVerified(true);
                $this->em->flush();
                return ['valid' => true];
            }

            $otp->setAttempts($otp->getAttempts() + 1);
            $this->em->flush();

            return [
                'valid'     => false,
                'error'     => 'BAD_CODE',
                'remaining' => max(0, $this->maxAttempts - $otp->getAttempts()),
            ];
        } catch (\Throwable) {
            return ['valid' => false, 'error' => 'SERVER_ERROR'];
        }
    }
}

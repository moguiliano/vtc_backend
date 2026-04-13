<?php

namespace App\Controller;

use App\Service\OtpService;
use App\Service\PhoneNormalizerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OtpController extends AbstractController
{
    public function __construct(
        private OtpService $otpService,
        private PhoneNormalizerService $phoneNormalizer,
    ) {}

    #[Route('/otp/send', name: 'otp_send', methods: ['POST'])]
    public function send(Request $request): JsonResponse
    {
        $rawPhone    = trim((string) $request->request->get('phone', ''));
        $countryCode = trim((string) $request->request->get('countryCode', ''));
        $guestPhone  = trim((string) $request->request->get('guestPhone', ''));

        $to = $this->phoneNormalizer->fromParts($rawPhone, $countryCode, $guestPhone);
        if (!$to) {
            return $this->json(['status' => 'error', 'error' => 'PHONE_INVALID'], 400);
        }

        $result = $this->otpService->sendOtp($to);

        return match ($result['status']) {
            'sent'     => $this->json(['status' => 'sent', 'expiresIn' => $result['expiresIn']]),
            'cooldown' => $this->json(['status' => 'sent', 'cooldown' => $result['cooldown']]),
            default    => $this->json(['status' => 'error', 'message' => $result['message'] ?? 'Erreur interne'], 500),
        };
    }

    #[Route('/otp/verify', name: 'otp_verify', methods: ['POST'])]
    public function verify(Request $request): JsonResponse
    {
        $rawPhone    = trim((string) $request->request->get('phone', ''));
        $countryCode = trim((string) $request->request->get('countryCode', ''));
        $guestPhone  = trim((string) $request->request->get('guestPhone', ''));
        $code        = trim((string) $request->request->get('code', ''));

        $to = $this->phoneNormalizer->fromParts($rawPhone, $countryCode, $guestPhone);
        if (!$to || $code === '') {
            return $this->json(['valid' => false, 'error' => 'INPUT_MISSING'], 400);
        }

        $result = $this->otpService->verifyCode($to, $code);

        $status = ($result['valid'] ?? false) ? 200 : 400;
        if (($result['error'] ?? '') === 'TOO_MANY_ATTEMPTS') {
            $status = 429;
        }

        return $this->json($result, $status);
    }
}

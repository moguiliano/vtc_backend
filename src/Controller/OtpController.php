<?php
// src/Controller/OtpController.php
namespace App\Controller;

use App\Service\TwilioService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OtpController extends AbstractController
{
    public function __construct(private TwilioService $twilio) {}

    #[Route('/otp/send', name: 'otp_send', methods: ['POST'])]
    public function send(Request $request): JsonResponse
    {
        $session = $request->getSession();

        // 1) Récupère ce que tu envoies depuis Tab 3
        $rawPhone     = trim((string) $request->request->get('phone', ''));
        $countryCode  = trim((string) $request->request->get('countryCode', ''));
        $guestPhone   = trim((string) $request->request->get('guestPhone', ''));

        // 2) Construit / normalise en E.164
        $to = $this->toE164($rawPhone, $countryCode, $guestPhone);
        if (!$to) {
            return $this->json(['status' => 'error', 'error' => 'PHONE_INVALID'], 400);
        }

        // Anti-spam: 1 SMS / 60s
        $key = 'otp.'.$to;
        $now = time();
        $entry = $session->get($key);
        if ($entry && isset($entry['lastSentAt']) && ($now - $entry['lastSentAt'] < 60)) {
            return $this->json(['status' => 'sent', 'cooldown' => 60 - ($now - $entry['lastSentAt'])]);
        }

        // 3) Génère et stocke le code (6 chiffres, 5 min)
        $code = (string) random_int(100000, 999999);
        $session->set($key, [
            'code'       => $code,
            'expiresAt'  => $now + 5 * 60,
            'attempts'   => 0,
            'lastSentAt' => $now,
            'verified'   => false,
        ]);

        // 4) Envoi SMS via Twilio
        $body = "ZenCAR: votre code est $code (valable 5 min). Ne le partagez pas.";
        try {
            $sid = $this->twilio->sendSms($to, $body);
        } catch (\Throwable $e) {
            return $this->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }

        return $this->json(['status' => 'sent', 'sid' => $sid, 'expiresIn' => 300]);
    }

    #[Route('/otp/verify', name: 'otp_verify', methods: ['POST'])]
    public function verify(Request $request): JsonResponse
    {
        $session = $request->getSession();

        $rawPhone    = trim((string) $request->request->get('phone', ''));
        $countryCode = trim((string) $request->request->get('countryCode', ''));
        $guestPhone  = trim((string) $request->request->get('guestPhone', ''));
        $code        = trim((string) $request->request->get('code', ''));

        $to = $this->toE164($rawPhone, $countryCode, $guestPhone);
        if (!$to || $code === '') {
            return $this->json(['valid' => false, 'error' => 'INPUT_MISSING'], 400);
        }

        $key = 'otp.'.$to;
        $entry = $session->get($key);
        if (!$entry) {
            return $this->json(['valid' => false, 'error' => 'NO_CODE'], 400);
        }

        $now = time();
        if ($now > (int) $entry['expiresAt']) {
            return $this->json(['valid' => false, 'error' => 'EXPIRED'], 400);
        }

        if ((int) $entry['attempts'] >= 5) {
            return $this->json(['valid' => false, 'error' => 'TOO_MANY_ATTEMPTS'], 429);
        }

        if (hash_equals((string)$entry['code'], $code)) {
            $entry['verified'] = true;
            $session->set($key, $entry);
            return $this->json(['valid' => true]);
        }

        $entry['attempts'] = (int)$entry['attempts'] + 1;
        $session->set($key, $entry);
        return $this->json(['valid' => false, 'error' => 'BAD_CODE', 'remaining' => max(0, 5 - (int)$entry['attempts'])], 400);
    }

    /** Construit un E.164 à partir de `phone` OU (`countryCode` + `guestPhone`) */
    private function toE164(string $rawPhone, string $countryCode, string $guestPhone): ?string
    {
        $digits = fn(string $s) => preg_replace('/\\D+/', '', $s);

        if ($rawPhone !== '') {
            // Si déjà +… correct
            if (preg_match('/^\\+[1-9]\\d{5,14}$/', $rawPhone)) return $rawPhone;
            // Sinon tente de le convertir
            $r = ltrim($rawPhone, '+');
            if (preg_match('/^[1-9]\\d{5,14}$/', $r)) return '+'.$r;
        }

        // Construction depuis countryCode + guestPhone : ex (“+33”, “0612345678”) → “+33612345678”
        if ($countryCode !== '' && $guestPhone !== '') {
            $cc = '+'.ltrim($digits($countryCode), '0');     // +33
            $ln = $digits($guestPhone);                      // 0612345678 → 612345678
            if (str_starts_with($ln, '0')) $ln = substr($ln, 1);
            $e164 = $cc.$ln;
            if (preg_match('/^\\+[1-9]\\d{5,14}$/', $e164)) return $e164;
        }

        return null;
    }
}

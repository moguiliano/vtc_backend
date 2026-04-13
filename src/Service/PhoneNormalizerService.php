<?php

namespace App\Service;

class PhoneNormalizerService
{
    /**
     * Normalise un numéro brut en E.164.
     * Utilisé par HomeController et SmsNotifier.
     *
     * Ex: "0612345678" (FR) → "+33612345678"
     *     "0033612345678"   → "+33612345678"
     *     "+33612345678"    → "+33612345678"
     */
    public function normalize(?string $raw, string $defaultCountry = 'FR'): ?string
    {
        if (!$raw) {
            return null;
        }

        $n = preg_replace('/[^\d+]/', '', $raw) ?? '';

        if (str_starts_with($n, '00')) {
            $n = '+' . substr($n, 2);
        }

        if (str_starts_with($n, '+')) {
            return $n;
        }

        if ($defaultCountry === 'FR' && preg_match('/^0\d{9}$/', $n)) {
            return '+33' . substr($n, 1);
        }

        return $n ?: null;
    }

    /**
     * Construit un E.164 depuis les données intl-tel-input (Tab3).
     * Utilisé par OtpController.
     *
     * Ex: rawPhone="+33612345678"          → "+33612345678"
     *     countryCode="+33", guestPhone="0612345678" → "+33612345678"
     */
    public function fromParts(string $rawPhone, string $countryCode, string $guestPhone): ?string
    {
        $digits = static fn(string $s): string => preg_replace('/\D+/', '', $s) ?? '';

        if ($rawPhone !== '') {
            if (preg_match('/^\+[1-9]\d{5,14}$/', $rawPhone)) {
                return $rawPhone;
            }
            $r = ltrim($rawPhone, '+');
            if (preg_match('/^[1-9]\d{5,14}$/', $r)) {
                return '+' . $r;
            }
        }

        if ($countryCode !== '' && $guestPhone !== '') {
            $cc = '+' . ltrim($digits($countryCode), '0');
            $ln = $digits($guestPhone);
            if (str_starts_with($ln, '0')) {
                $ln = substr($ln, 1);
            }
            $e164 = $cc . $ln;
            if (preg_match('/^\+[1-9]\d{5,14}$/', $e164)) {
                return $e164;
            }
        }

        return null;
    }
}

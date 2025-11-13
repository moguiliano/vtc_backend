<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Reservation;
use Twilio\Rest\Client;
use Psr\Log\LoggerInterface;

/**
 * SmsNotifier
 * ---------------
 * Service chargé de :
 *  - Construire les messages de confirmation d'une réservation.
 *  - Envoyer les SMS au client et aux administrateurs.
 *
 * Principes de conception
 *  - Le service ne dépend que de l'entité Reservation + overrides optionnels du front.
 *  - Normalisation des numéros au format E.164 (par défaut FR) pour Twilio.
 *  - Code robuste : tous les getters sont appelés de manière défensive.
 *  - Aucune logique métier de pricing / routing ici : uniquement la notification.
 */
final class SmsNotifier
{
    /** @var Client Client Twilio injecté */
    private Client $twilio;

    /** @var string Numéro d'expéditeur Twilio (E.164) */
    private string $twilioFrom;

    /** @var string[] Liste de destinataires admin (E.164) */
    private array $adminNumbers;

    /** @var LoggerInterface|null Logger (optionnel) */
    private ?LoggerInterface $logger;

    /**
     * @param Client               $twilio        Client Twilio
     * @param string               $twilioFrom    Numéro Twilio expéditeur (ex: +33600000000)
     * @param string[]             $adminNumbers  Liste de numéros admin (peut être vide)
     * @param LoggerInterface|null $logger        Logger optionnel
     */
    public function __construct(
        Client $twilio,
        string $twilioFrom,
        array $adminNumbers = [],
        ?LoggerInterface $logger = null
    ) {
        $this->twilio       = $twilio;
        $this->twilioFrom   = $twilioFrom;
        $this->adminNumbers = $adminNumbers;
        $this->logger       = $logger;
    }

    /**
     * Envoie les SMS pour une réservation.
     *
     * @param Reservation $r              Entité réservation déjà persistée (id disponible)
     * @param ?string     $clientPhone    Override du numéro client depuis le front (ex: Tab3 non mappé)
     * @param ?string     $prenomOverride Override du prénom client (depuis le front)
     * @param string      $defaultCountry Code pays par défaut pour la normalisation (ex: 'FR')
     */
    public function notifyReservation(
        Reservation $r,
        ?string $clientPhone = null,
        ?string $prenomOverride = null,
        string $defaultCountry = 'FR'
    ): void {
        // 1) Construire les deux messages (client + admin)
        [$clientMsg, $adminMsg] = $this->buildMessagePair($r, $prenomOverride);

        // 2) Déterminer le numéro du client (override front > entité liée) puis normaliser
        $rawClient = $clientPhone ?: $this->extractClientPhone($r);
        $toClient  = $this->normalizeToE164Like($rawClient, $defaultCountry);

        // 3) Envoi client (si présent)
        if ($toClient) {
            $this->sendSms($toClient, $clientMsg);
        } else {
            $this->log('info', 'Aucun numéro client détecté pour la réservation.', ['reservationId' => $this->safeId($r)]);
        }

        // 4) Envoi admins (si configurés)
        foreach ($this->adminNumbers as $adminRaw) {
            $toAdmin = $this->normalizeToE164Like($adminRaw, $defaultCountry);
            if ($toAdmin) {
                $this->sendSms($toAdmin, $adminMsg);
            }
        }
    }

    // ---------------------------------------------------------------------
    // Construction des messages
    // ---------------------------------------------------------------------

    /**
     * Construit le couple (message client, message admin).
     * @return array{0:string,1:string}
     */
    private function buildMessagePair(Reservation $r, ?string $prenomOverride = null): array
    {
        // 1) Immediacy (Immédiat / Plus tard) à partir de l'heure de prise en charge
        $immediacy = $this->computeImmediacy($this->callIfAvailable($r, ['getDateHeureDepart']));

        // 2) Prénom prioritaire (override > Reservation::getPrenom > Client::getFirstname)
        $prenom = $this->preferNonEmpty(
            [
                $prenomOverride,
                $this->callIfAvailable($r, ['getPrenom']),
                $this->callIfAvailable($this->callIfAvailable($r, ['getClient', 'getUser']), ['getFirstname', 'getFirstName']),
            ]
        );

        // 3) Champs principaux
        $depart       = (string) ($this->callIfAvailable($r, ['getDepart']) ?? '—');
        $arrivee      = (string) ($this->callIfAvailable($r, ['getArrivee']) ?? '—');
        $vehicule     = (string) ($this->callIfAvailable($r, ['getTypeVehicule']) ?? '—');
        $pickupAt     = $this->formatDt($this->callIfAvailable($r, ['getDateHeureDepart']));
        $createdAt    = $this->formatDt($this->callIfAvailable($r, ['getCreatedAt']));

        // 4) Stop option
        $stopLine = '';
        $hasStop  = (bool) ($this->callIfAvailable($r, ['getStopOption']) ?? false);
        if ($hasStop) {
            $stop    = trim((string) ($this->callIfAvailable($r, ['getStopLieu']) ?? ''));
            $stopLine = "\nArrêt : " . ($stop !== '' ? $stop : 'Oui');
        }

        // 5) Siège bébé
        $siege = (bool) ($this->callIfAvailable($r, ['isSiegeBebe']) ?? false) ? 'Oui' : 'Non';

        // 6) Mesures (avec fallback)
        $distance = $this->formatUnit($this->callIfAvailable($r, ['getDistance']), ' km');
        $duree    = $this->formatUnit($this->callIfAvailable($r, ['getDuree']), ' min');
        $prix     = $this->formatUnit($this->callIfAvailable($r, ['getPrix']), ' €');

        // 7) Tronc commun
        $base =
            "{$immediacy} • {$prenom}\n" .
            "Départ : {$depart}\n" .
            "Arrivée : {$arrivee}\n" .
            "Prise en charge : {$pickupAt}{$stopLine}\n" .
            "Siège bébé : {$siege}\n" .
            "Véhicule : {$vehicule}\n" .
            "Distance : {$distance} • Durée : {$duree}\n" .
            "Prix : {$prix}";

        // 8) Messages finaux
        $clientMsg = "✅ Confirmation ZenCAR\n{$base}\nRéservation créée : {$createdAt}\nMerci pour votre confiance.";
        $adminMsg  = "🆕 Nouvelle réservation\n{$base}\nCréée le : {$createdAt}";

        return [$clientMsg, $adminMsg];
    }

    /**
     * Déduit "Immédiat" si la prise en charge est dans <= 15 minutes, sinon "Plus tard".
     */
    private function computeImmediacy(?\DateTimeInterface $pickup): string
    {
        if (!$pickup) {
            return 'Plus tard';
        }
        $now  = new \DateTimeImmutable('now');
        $diff = $pickup->getTimestamp() - $now->getTimestamp();
        return ($diff <= 15 * 60) ? 'Immédiat' : 'Plus tard';
    }

    // ---------------------------------------------------------------------
    // Téléphone & utilitaires
    // ---------------------------------------------------------------------

    /**
     * Tente d'extraire un numéro de téléphone client depuis les entités liées.
     * - Priorité à Reservation->getClient() / getUser() puis getters usuels.
     * - En dernier recours Reservation->getPhone() si présent.
     */
    private function extractClientPhone(Reservation $r): ?string
    {
        $user = $this->callIfAvailable($r, ['getClient', 'getUser']);
        if ($user && \is_object($user)) {
            $phone = $this->callIfAvailable($user, ['getPhone', 'getTelephone', 'getTel', 'getMobile', 'getMobilePhone']);
            if (\is_string($phone) && $phone !== '') {
                return $phone;
            }
        }

        return $this->callIfAvailable($r, ['getPhone']);
    }

    /**
     * Normalise un numéro "humain" en forme E.164-like selon un pays par défaut.
     * - Supprime les séparateurs
     * - Convertit '00' en '+'
     * - FR : transforme 0XXXXXXXXX -> +33XXXXXXXXX
     */
    private function normalizeToE164Like(?string $raw, string $defaultCountry = 'FR'): ?string
    {
        if (!$raw) {
            return null;
        }

        // Garder uniquement chiffres et '+'
        $n = \preg_replace('/[^\d+]/', '', $raw) ?? '';

        // 00 -> +
        if (\str_starts_with($n, '00')) {
            $n = '+' . \substr($n, 2);
        }

        // Déjà au format international
        if (\str_starts_with($n, '+')) {
            return $n;
        }

        // Règle FR : 0********* -> +33*********
        if ($defaultCountry === 'FR' && \preg_match('/^0\d{9}$/', $n) === 1) {
            return '+33' . \substr($n, 1);
        }

        // Sinon retourner tel quel (Twilio rejettera s'il est invalide)
        return $n ?: null;
    }

    /**
     * Envoie un SMS via Twilio avec gestion d'erreur et log.
     */
    private function sendSms(string $to, string $body): void
    {
        try {
            $this->twilio->messages->create($to, [
                'from' => $this->twilioFrom,
                'body' => $body,
            ]);
            $this->log('info', 'SMS envoyé', ['to' => $to]);
        } catch (\Throwable $e) {
            $this->log('error', 'Échec envoi SMS', ['to' => $to, 'error' => $e->getMessage()]);
            // On ne relance pas l'exception pour ne pas bloquer la réservation.
        }
    }

    /**
     * Appelle la première méthode existante et callable d'une liste, sinon null.
     * @template T of object
     * @param T|mixed $object
     * @param string[] $methods
     * @return mixed
     */
    private function callIfAvailable($object, array $methods)
    {
        if (!\is_object($object)) {
            return null;
        }

        foreach ($methods as $m) {
            if (\is_string($m) && \method_exists($object, $m) && \is_callable([$object, $m])) {
                try {
                    return $object->$m();
                } catch (\Throwable) {
                    // Ignorer silencieusement (getter inexistant fonctionnellement)
                }
            }
        }
        return null;
    }

    /**
     * Renvoie la première valeur non vide d'une liste.
     * @param array<int, mixed> $values
     */
    private function preferNonEmpty(array $values): string
    {
        foreach ($values as $v) {
            if (\is_string($v) && \trim($v) !== '') {
                return (string) $v;
            }
        }
        return '';
    }

    /**
     * Formate une DateTime (d/m/Y H:i) ou renvoie '—' si absente.
     */
    private function formatDt(?\DateTimeInterface $dt): string
    {
        return $dt ? $dt->format('d/m/Y H:i') : '—';
    }

    /**
     * Formate une valeur numérique avec unité ou renvoie '—' si absente.
     * @param mixed $v
     */
    private function formatUnit($v, string $suffix): string
    {
        if ($v === null || $v === '') {
            return '—';
        }
        // cast sûr (int|float|string numeric)
        if (\is_numeric($v)) {
            return (string) ($v . $suffix);
        }
        return (string) $v;
    }

    /**
     * Récupère un identifiant de réservation "safe" pour les logs (si disponible).
     */
    private function safeId(Reservation $r): ?int
    {
        $id = $this->callIfAvailable($r, ['getId']);
        return (\is_int($id) ? $id : null);
    }

    /**
     * Log utilitaire (n'envoie rien si pas de logger).
     * @param 'info'|'error' $level
     * @param string $message
     * @param array<string, mixed> $context
     */
    private function log(string $level, string $message, array $context = []): void
    {
        if (!$this->logger) {
            return;
        }
        if ($level === 'error') {
            $this->logger->error($message, $context);
        } else {
            $this->logger->info($message, $context);
        }
    }
}

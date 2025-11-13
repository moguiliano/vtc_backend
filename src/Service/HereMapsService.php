<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Service qui centralise toutes les fonctionnalités liées à l'API HERE et au calcul de prix.
 */
class HereMapsService
{
    private HttpClientInterface $client;
    private string $apiKey;

    public function __construct(HttpClientInterface $client, ParameterBagInterface $params)
    {
        $this->client = $client;
        $this->apiKey = $params->get('here_api_key');
    }

    /**
     * Appel HTTP GET à l'API HERE
     */
    private function fetchFromApi(string $url): ?array
    {
        $response = $this->client->request('GET', $url);
        if ($response->getStatusCode() !== 200) return null;

        try {
            return $response->toArray();
        } catch (\Exception) {
            return null;
        }
    }

    /**
     * Geocode une adresse en coordonnées GPS
     */
    public function geocodeAddress(string $address): ?array
    {
        $encoded = urlencode($address);
        $url = "https://geocode.search.hereapi.com/v1/geocode?q={$encoded}&apiKey={$this->apiKey}";
        $data = $this->fetchFromApi($url);
        return $data['items'][0]['position'] ?? null;
    }

    /**
     * Autocomplétion d'adresse centrée sur Marseille
     */
    public function autocompleteAddress(string $query): array
    {
        $encoded = urlencode($query);
        $url = "https://autosuggest.search.hereapi.com/v1/autosuggest?q={$encoded}&at=43.2965,5.3698&apiKey={$this->apiKey}";

        $data = $this->fetchFromApi($url);
        if (!$data) return [];

        $results = [];
        foreach ($data['items'] ?? [] as $item) {
            if (!isset($item['address']['label'])) continue;
            $results[] = [
                'label' => $item['address']['label'],
                'lat'   => $item['position']['lat'] ?? null,
                'lng'   => $item['position']['lng'] ?? null,
            ];
        }

        return $results;
    }


    /**
     * Distance & durée avec arrêt facultatif
     */
    public function getDistanceAndDurationWithStop(string $originAddress, string $destinationAddress, ?string $stopAddress = null): array
    {
        $origin = $this->geocodeAddress($originAddress);
        $destination = $this->geocodeAddress($destinationAddress);
        $via = $stopAddress ? $this->geocodeAddress($stopAddress) : null;
        if (!$origin || !$destination || ($stopAddress && !$via)) {
            return ['error' => 'Impossible de géocoder une ou plusieurs adresses'];
        }

        $url = "https://router.hereapi.com/v8/routes?transportMode=car";
        $url .= "&origin={$origin['lat']},{$origin['lng']}";
        if ($via) $url .= "&via={$via['lat']},{$via['lng']}";
        $url .= "&destination={$destination['lat']},{$destination['lng']}";
        $url .= "&return=summary&apiKey={$this->apiKey}";

        $data = $this->fetchFromApi($url);

        if (!$data || !isset($data['routes'][0]['sections'])) {
            return ['error' => 'Aucun itinéraire trouvé'];
        }

        $totalDistance = 0;
        $totalDuration = 0;
        foreach ($data['routes'][0]['sections'] as $section) {
            $summary = $section['summary'];
            $totalDistance += $summary['length'];
            $totalDuration += $summary['duration'];
        }

        return [
            'distance_km'   => round($totalDistance / 1000, 2),
            'duration_min'  => round($totalDuration / 60, 2)
        ];
    }

    /**
     * Estimation de prix d'un trajet selon la distance, heure et catégorie de véhicule
     */
    public function estimerPrix(float $distance, string $categorie, int $heure): ?array
    {
        $prix = match ($categorie) {
            'eco_berline'   => $this->calculEcoBerline($distance),
            'grand_coffre'  => $this->calculGrandCoffre($distance),
            'berline'       => $this->calculBerline($distance),
            'van'           => $this->calculVan($distance),
            default         => null,
        };

        if (!$prix) return null;

        if ($heure < 7 || $heure >= 23) {
            $prix['prix_total'] = round($prix['prix_total'] * 1.2, 2);
            $prix['commission'] = round($prix['prix_total'] * 0.20, 2);
            $prix['net_chauffeur'] = round($prix['prix_total'] - $prix['commission'], 2);
            $prix['majoration_nuit'] = true;
        } else {
            $prix['majoration_nuit'] = false;
        }

        return $prix;
    }

    public function getFullEstimation(string $depart, string $arrivee, ?string $stop, string $categorie, int $heure): array
    {
        $distanceInfos = $this->getDistanceAndDurationWithStop($depart, $arrivee, $stop);
        if (isset($distanceInfos['error'])) return $distanceInfos;

        $prixInfos = $this->estimerPrix($distanceInfos['distance_km'], $categorie, $heure);
        if (!$prixInfos) return ['error' => 'Erreur lors de l’estimation du prix.'];

        return array_merge($distanceInfos, $prixInfos);
    }

    private function formatPrix(float $prix): array
    {
        $prix = round($prix, 2);
        $commission = round($prix * 0.20, 2);
        $net = round($prix - $commission, 2);

        return [
            'prix_total' => $prix,
            'commission' => $commission,
            'net_chauffeur' => $net,
        ];
    }

    private function calculEcoBerline(float $distance): array
    {
        $prix = $distance <= 10 ? 20 + ($distance * 2.5) : 45 + (($distance - 10) * 2);
        return $this->formatPrix($prix);
    }

    private function calculGrandCoffre(float $distance): array
    {
        $prix = $distance <= 10 ? 30 + ($distance * 2.5) : 55 + (($distance - 10) * 2);
        return $this->formatPrix($prix);
    }

    private function calculBerline(float $distance): array
    {
        $prix = $distance <= 4 ? 35 : 35 + (($distance - 4) * 3.1);
        return $this->formatPrix($prix);
    }

    private function calculVan(float $distance): array
    {
        $prix = $distance <= 7 ? 63 : 63 + (($distance - 7) * 3.2);
        return $this->formatPrix($prix);
    }
}

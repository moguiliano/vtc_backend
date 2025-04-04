<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Service pour utiliser les fonctionnalités de HERE Maps :
 * - Géocodage d'adresses
 * - Calcul distance & durée
 * - Autocomplétion d'adresses
 */
class HereMapsService
{
    private $client;
    private $apiKey;

    public function __construct(HttpClientInterface $client, ParameterBagInterface $params)
    {
        $this->client = $client;
        $this->apiKey = $params->get('here_api_key');
    }

    /**
     * Convertit une adresse en coordonnées GPS (lat/lng).
     */
    public function geocodeAddress($address)
    {
        $encodedAddress = urlencode($address);
        $url = "https://geocode.search.hereapi.com/v1/geocode?q={$encodedAddress}&apiKey={$this->apiKey}";

        $response = $this->client->request('GET', $url);
        $data = $response->toArray();

        if (isset($data['items'][0]['position'])) {
            return [
                'lat' => $data['items'][0]['position']['lat'],
                'lng' => $data['items'][0]['position']['lng']
            ];
        }

        return null;
    }


    /**
     * Retourne une liste de suggestions d'adresses avec position GPS
     * dans un rayon de 100km autour de Marseille.
     */
    public function autocompleteAddress(string $query): array
{
    $encodedQuery = urlencode($query);
    $url = "https://autosuggest.search.hereapi.com/v1/autosuggest?"
    . "q={$encodedQuery}"
    . "&at=43.2965,5.3698"
    . "&apiKey={$this->apiKey}";


    $response = $this->client->request('GET', $url);
    $data = $response->toArray();

    $results = [];

    foreach ($data['items'] ?? [] as $item) {
        // On accepte seulement les lieux avec une adresse
        if (!isset($item['address']['label'])) continue;

        $results[] = [
            'label' => $item['address']['label'],
            'lat'   => $item['position']['lat'] ?? null,
            'lng'   => $item['position']['lng'] ?? null,
        ];
    }

    return $results;
}
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
    if ($via) {
        $url .= "&via={$via['lat']},{$via['lng']}";
    }
    $url .= "&destination={$destination['lat']},{$destination['lng']}";
    $url .= "&return=summary&apiKey={$this->apiKey}";

    $response = $this->client->request('GET', $url);

    if ($response->getStatusCode() !== 200) {
        return ['error' => 'Erreur lors de la requête vers HERE API'];
    }

    try {
        $data = $response->toArray();
    } catch (\Exception $e) {
        return ['error' => 'Réponse invalide de HERE API'];
    }

    if (isset($data['routes'][0]['sections'])) {
        $totalDistance = 0;
        $totalDuration = 0;

        foreach ($data['routes'][0]['sections'] as $section) {
            $summary = $section['summary'];
            $totalDistance += $summary['length'];
            $totalDuration += $summary['duration'];
        }

        return [
            'distance_km' => round($totalDistance / 1000, 2),
            'duration_min' => round($totalDuration / 60, 2)
        ];
    }

    return ['error' => 'Aucun itinéraire trouvé'];
}


}

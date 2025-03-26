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
        $this->apiKey = $params->get('here_api_key'); // clé API chargée depuis le .env
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
     * Calcule la distance et la durée entre deux adresses.
     */
    public function getDistanceAndDuration($originAddress, $destinationAddress)
    {
        $origin = $this->geocodeAddress($originAddress);
        $destination = $this->geocodeAddress($destinationAddress);

        if (!$origin || !$destination) {
            return ['error' => 'Impossible de géocoder une ou plusieurs adresses'];
        }

        $url = "https://router.hereapi.com/v8/routes?transportMode=car&origin={$origin['lat']},{$origin['lng']}&destination={$destination['lat']},{$destination['lng']}&return=summary&apiKey={$this->apiKey}";

        $response = $this->client->request('GET', $url);

        if ($response->getStatusCode() !== 200) {
            return ['error' => 'Erreur lors de la requête vers HERE API'];
        }

        try {
            $data = $response->toArray();
        } catch (\Exception $e) {
            return ['error' => 'Réponse invalide de HERE API'];
        }

        if (isset($data['routes'][0]['sections'][0]['summary'])) {
            $summary = $data['routes'][0]['sections'][0]['summary'];
            return [
                'distance_km' => round($summary['length'] / 1000, 2),
                'duration_min' => round($summary['duration'] / 60, 2)
            ];
        }

        return ['error' => 'Aucun itinéraire trouvé'];
    }

    /**
     * Retourne une liste de suggestions d'adresses en fonction d'une requête utilisateur.
     */
    public function autocompleteAddress(string $query): array
    {
        $encodedQuery = urlencode($query);
        $url = "https://autocomplete.search.hereapi.com/v1/autocomplete?q={$encodedQuery}&apiKey={$this->apiKey}";

        $response = $this->client->request('GET', $url);
        $data = $response->toArray();

        $suggestions = [];

        foreach ($data['items'] ?? [] as $item) {
            $suggestions[] = $item['address']['label'];
        }

        return $suggestions;
    }
}

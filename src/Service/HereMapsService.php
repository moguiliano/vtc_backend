<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HereMapsService
{
    private string $apiKey;

    public function __construct(
        private HttpClientInterface $client,
        ParameterBagInterface $params,
    ) {
        $this->apiKey = $params->get('here_api_key');
    }

    // -------------------------------------------------------------------------
    // API HERE
    // -------------------------------------------------------------------------

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

    public function geocodeAddress(string $address): ?array
    {
        $url = "https://geocode.search.hereapi.com/v1/geocode?q=" . urlencode($address) . "&apiKey={$this->apiKey}";
        $data = $this->fetchFromApi($url);
        return $data['items'][0]['position'] ?? null;
    }

    public function autocompleteAddress(string $query): array
    {
        $url = "https://autosuggest.search.hereapi.com/v1/autosuggest?q=" . urlencode($query) . "&at=43.2965,5.3698&apiKey={$this->apiKey}";
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

    public function getDistanceAndDurationWithStop(string $originAddress, string $destinationAddress, ?string $stopAddress = null): array
    {
        $origin      = $this->geocodeAddress($originAddress);
        $destination = $this->geocodeAddress($destinationAddress);
        $via         = $stopAddress ? $this->geocodeAddress($stopAddress) : null;

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
            $totalDistance += $section['summary']['length'];
            $totalDuration += $section['summary']['duration'];
        }

        return [
            'distance_km'  => round($totalDistance / 1000, 2),
            'duration_min' => round($totalDuration / 60, 2),
        ];
    }
}

<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class HereMapsService
{
    private $client;
    private $apiKey;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
        $this->apiKey = '5vQSLKBwontpC6yqQeoA9Hp5_ytsyeN1SldFAQW1Ks8'; // Remplace par ta clé API
    }
        
    // 🗺️ Convertit une adresse en coordonnées GPS

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
        // 🚗 Récupère la distance et la durée en utilisant les coordonnées GPS

        public function getDistanceAndDuration($originAddress, $destinationAddress)
        {
            // 🗺️ Convertir les adresses en coordonnées GPS
            $origin = $this->geocodeAddress($originAddress);
            $destination = $this->geocodeAddress($destinationAddress);
        
            if (!$origin || !$destination) {
                return ['error' => 'Impossible de géocoder une ou plusieurs adresses'];
            }
        
            // Construire l'URL avec les coordonnées GPS
            $url = "https://router.hereapi.com/v8/routes?transportMode=car&origin={$origin['lat']},{$origin['lng']}&destination={$destination['lat']},{$destination['lng']}&return=summary&apiKey={$this->apiKey}";
        
            // 🛠 Debug : Log l'URL pour voir si elle est bien formatée
            error_log("HERE Routing Request: " . $url);
        
            $response = $this->client->request('GET', $url);
        
            // Vérifier le statut HTTP de la réponse
            if ($response->getStatusCode() !== 200) {
                error_log("Erreur HERE API: " . $response->getContent(false)); // Log l'erreur
                return ['error' => 'Erreur lors de la requête vers HERE API'];
            }
        
            // Essayer de parser la réponse
            try {
                $data = $response->toArray();
            } catch (\Exception $e) {
                error_log("Erreur de conversion JSON: " . $e->getMessage());
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
        
}

// main.js – Fichier principal de ZenCar (sans Webpack)
// ⚠️ Ce fichier suppose que les autres fichiers JS (autocomplete.js, map.js, ui.js, reservation.js)
// ont été inclus AVANT dans le HTML, ou bien intégrés dans le même bundle. Toutes les fonctions sont appelées via `window`.

(function () {
  // ✅ Une fois le DOM entièrement chargé, on initialise tous les modules
  document.addEventListener("DOMContentLoaded", () => {
    // 🔐 Initialise la clé API HERE pour le module Autocomplete
    window.Autocomplete.initApiKey(window.HERE_API_KEY);

    // 🚀 Active l'autocomplétion sur les champs de réservation
    window.Autocomplete.init([
      'reservation_depart',
      'reservation_arrivee',
      'reservation_stopLieu'
    ]);

    // 🧩 Composants de l'interface utilisateur
    window.UI.setupStopCheckbox(); // Affichage/masquage du champ "Arrêt"
    window.UI.setupVehicleSelection(); // Sélection du type de véhicule
    window.UI.setupTimeChoiceButtons(); // Choix immédiat ou planifié

    // 📦 Logique de réservation (validation + envoi serveur + affichage récapitulatif)
    window.Reservation.setupReservationHandlers();

    // 🗺️ Initialisation de la carte HERE avec géocodage et marqueurs
    window.ZenMap.initMap();
  });
})();
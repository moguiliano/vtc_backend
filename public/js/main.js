// ============================================================================
// 📂 main.js – Fichier principal de ZenCar (sans Webpack)
// ============================================================================
// ⚠️ Ce fichier suppose que les autres fichiers JS (autocomplete.js, map.js,
// ui.js, reservation.js) ont été inclus AVANT dans le HTML, ou bien intégrés
// ensemble dans un bundle unique.
// ============================================================================

(function () {
  // Exécute le script seulement quand le DOM est totalement chargé
  document.addEventListener("DOMContentLoaded", () => {
    // =========================================================================
    // 🔐 Étape 1 : Récupère dynamiquement la clé API HERE depuis Symfony
    // -------------------------------------------------------------------------
    // - Appelle la route sécurisée /api/get-here-key
    // - Réponse attendue en JSON : { key: "..."} ou { apiKey: "..." }
    // - Vérifie que la clé est présente, sinon log une erreur
    // =========================================================================
    fetch("/api/get-here-key")
      .then((res) => res.json())
      .then((data) => {
        const apiKey = data.key || data.apiKey; // ✅ compatibilité "key" ou "apiKey"
        if (!apiKey) {
          console.error("🛑 Clé HERE API manquante dans la réponse JSON.");
          return;
        }

        // =========================================================================
        // 🔐 Étape 2 : Initialise la clé pour Autocomplete
        // -------------------------------------------------------------------------
        // - Configure window.Autocomplete (défini dans autocomplete.js)
        // - Stocke la clé pour les appels à HERE Autosuggest / Reverse Geocode
        // =========================================================================
        window.Autocomplete.initApiKey(apiKey);

        // =========================================================================
        // 🚀 Étape 3 : Autocomplétion sur les champs du formulaire de réservation
        // -------------------------------------------------------------------------
        // - Attache l’autocomplétion aux inputs :
        //    • Départ (#reservation_depart)
        //    • Arrivée (#reservation_arrivee)
        //    • Arrêt (#reservation_stopLieu)
        // - Les suggestions se déclenchent à partir de 3 caractères
        // =========================================================================
        window.Autocomplete.init([
          "reservation_depart",
          "reservation_arrivee",
          "reservation_stopLieu",
        ]);

        // =========================================================================
        // 🧩 Étape 4 : Activation des composants d'interface utilisateur
        // -------------------------------------------------------------------------
        // - setupStopCheckbox() : affiche/masque le champ "Arrêt"
        // - setupVehicleSelection() : gestion de la sélection des véhicules
        // - setupTimeChoiceButtons() : choix "Immédiat" ou "Plus tard"
        // =========================================================================
        window.UI.setupStopCheckbox();
        window.UI.setupVehicleSelection();
        window.UI.setupTimeChoiceButtons();

        // =========================================================================
        // 📦 Étape 5 : Gestion du formulaire de réservation
        // -------------------------------------------------------------------------
        // - setupReservationHandlers() gère les boutons :
        //   • "goToVehicle" → passe à l’étape véhicule + calcule trajet/prix
        //   • "nextRecapBtn" → génère le récapitulatif final + onglet 3
        // =========================================================================
        window.Reservation.setupReservationHandlers();

        // =========================================================================
        // 🗺️ Étape 6 : Initialisation de la carte HERE
        // -------------------------------------------------------------------------
        // - Passe la clé API sécurisée à ZenMap.initMap()
        // - Affiche la carte centrée sur Marseille par défaut
        // - Ajoute écouteurs pour synchroniser adresses ↔ carte
        // =========================================================================
        window.ZenMap.initMap(apiKey);
      })
      .catch((error) => {
        // ⚠️ Gestion des erreurs réseau ou backend
        console.error("🛑 Erreur lors de la récupération de la clé HERE:", error);
      });
  });
})();

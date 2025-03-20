  const ReservationApp = {
    apiKey: "",

    /**
     * Charge la clé API depuis l'endpoint Symfony '/get-api-key'
     */
    loadApiKey: async function () {
      try {
        const response = await fetch('/get-api-key');
        const data = await response.json();
        this.apiKey = data.apiKey;
        console.log("Clé API chargée.");
      } catch (error) {
        console.error("Erreur lors de la récupération de la clé API :", error);
      }
    },

    /**
     * Récupère et affiche les suggestions d'adresse pour un champ donné.
     * @param {string} inputId - L'ID du champ de saisie.
     */
    fetchSuggestions: async function (inputId) {
      const inputField = document.getElementById(inputId);
      const suggestionsList = document.getElementById(`${inputId}-suggestions`);
      const query = inputField.value.trim();

      // Si la saisie est trop courte, on vide la liste des suggestions
      if (query.length < 3) {
        if (suggestionsList) {
          suggestionsList.innerHTML = "";
        }
        return;
      }

      // Construction de l'URL de l'API HERE pour l'autocomplétion
      const url = `https://geocode.search.hereapi.com/v1/geocode?q=${encodeURIComponent(query)}&apikey=${this.apiKey}`;

      try {
        const response = await fetch(url);
        if (!response.ok) {
          throw new Error(`Erreur HTTP : ${response.status}`);
        }
        const data = await response.json();
        this.displaySuggestions(data.items, inputId);
      } catch (error) {
        console.error("Erreur lors de la requête :", error);
      }
    },

    /**
     * Affiche la liste des suggestions sous le champ de saisie.
     * @param {Array} suggestions - Liste d'objets suggestion.
     * @param {string} inputId - L'ID du champ de saisie.
     */
    displaySuggestions: function (suggestions, inputId) {
      const inputField = document.getElementById(inputId);
      let suggestionsList = document.getElementById(`${inputId}-suggestions`);
      
      // Si la liste n'existe pas, on la crée
      if (!suggestionsList) {
        suggestionsList = document.createElement("ul");
        suggestionsList.id = `${inputId}-suggestions`;
        suggestionsList.classList.add("suggestions-list");
        inputField.parentNode.appendChild(suggestionsList);
      }

      // Réinitialiser la liste des suggestions
      suggestionsList.innerHTML = "";

      if (!suggestions || suggestions.length === 0) {
        suggestionsList.innerHTML = "<li>Aucune suggestion trouvée</li>";
        return;
      }

      suggestions.forEach(item => {
        const li = document.createElement("li");
        li.textContent = item.title;
        li.addEventListener("click", () => {
          inputField.value = item.title;
          suggestionsList.innerHTML = "";
        });
        suggestionsList.appendChild(li);
      });
    },

    /**
     * Configure l'autocomplétion sur un champ donné.
     * @param {string} inputId - L'ID du champ de saisie.
     */
    setupAutocomplete: function (inputId) {
      const inputField = document.getElementById(inputId);
      if (inputField) {
        inputField.setAttribute("autocomplete", "off");
        inputField.addEventListener("keyup", () => this.fetchSuggestions(inputId));
      }
    },

    /**
     * Crée et ajoute un bouton de géolocalisation pour un champ donné.
     * @param {string} inputId - L'ID du champ de saisie.
     */
    setupGeolocation: function (inputId) {
      const inputField = document.getElementById(inputId);
      if (inputField) {
        // Éviter d'ajouter plusieurs boutons pour le même champ
        if (!inputField.parentNode.querySelector(`button[data-target="${inputId}"]`)) {
          const geoButton = document.createElement("button");
          geoButton.type = "button";
          geoButton.textContent = "📍 Utiliser ma position";
          geoButton.classList.add("geoButton");
          geoButton.setAttribute("data-target", inputId);
          geoButton.addEventListener("click", () => this.useGeolocation(inputId));
          inputField.parentNode.appendChild(geoButton);
        }
      }
    },

    /**
     * Utilise la géolocalisation pour remplir un champ avec l'adresse actuelle.
     * @param {string} inputId - L'ID du champ de saisie.
     */
    useGeolocation: async function (inputId) {
      if (!navigator.geolocation) {
        alert("La géolocalisation n'est pas supportée par votre navigateur.");
        return;
      }

      navigator.geolocation.getCurrentPosition(
        async (position) => {
          const lat = position.coords.latitude;
          const lng = position.coords.longitude;
          const url = `https://revgeocode.search.hereapi.com/v1/revgeocode?at=${lat},${lng}&apikey=${this.apiKey}`;

          try {
            const response = await fetch(url);
            if (!response.ok) {
              throw new Error(`Erreur HTTP : ${response.status}`);
            }
            const data = await response.json();
            if (data.items && data.items.length > 0) {
              document.getElementById(inputId).value = data.items[0].address.label;
            } else {
              alert("Impossible de trouver votre adresse.");
            }
          } catch (error) {
            console.error("Erreur lors de la récupération de l'adresse :", error);
          }
        },
        (error) => {
          alert("Impossible d'obtenir votre position. Vérifiez vos autorisations.");
          console.error("Erreur de géolocalisation :", error);
        }
      );
    },

    /**
     * Initialise l'application : charge la clé API et configure les fonctionnalités.
     */
    init: async function () {
      // Charger la clé API
      await this.loadApiKey();

      // Liste des IDs des champs à configurer
      const inputIds = [
        'reservation_depart',
        'reservation_arrivee',
        'reservation_lieuArret'
      ];
      inputIds.forEach(id => {
        this.setupAutocomplete(id);
        this.setupGeolocation(id);
      });

      // Gestion de l'affichage du champ d'arrêt en fonction de la checkbox
      const stopCheckbox = document.getElementById("reservation_Stop");
      const stopoverLocation = document.getElementById("stopover-location");
      if (stopCheckbox && stopoverLocation) {
        stopCheckbox.addEventListener("change", function () {
          stopoverLocation.style.display = this.checked ? "block" : "none";
        });
      }
    }
  };

  // Masquer les listes de suggestions lorsqu'on clique en dehors du champ
  document.addEventListener("click", (event) => {
    document.querySelectorAll(".suggestions-list").forEach(list => {
      if (!list.previousElementSibling.contains(event.target) &&
          !list.contains(event.target)) {
        list.innerHTML = "";
      }
    });
  });

  // Initialiser l'application lorsque le DOM est entièrement chargé
  document.addEventListener("DOMContentLoaded", () => {
    ReservationApp.init();
  });

  // --------------------------------------------------
  // Gestion du formulaire de réservation
  // --------------------------------------------------
  document.getElementById('reservation-form').addEventListener('submit', function (event) {
    event.preventDefault(); // Empêche la soumission classique du formulaire

    // Récupérer les valeurs des champs "départ" et "arrivée"
    const origin = document.getElementById('reservation_depart').value;
    const destination = document.getElementById('reservation_arrivee').value;

    if (!origin || !destination) {
      alert("Veuillez saisir une adresse de départ et d'arrivée !");
      return;
    }

    // Envoie une requête POST pour récupérer la distance et la durée estimée
    fetch('/distance', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ origin, destination })
    })
      .then(response => response.json())
      .then(data => {
        if (data.error) {
          alert(data.error);
        } else {
          // Affiche les résultats dans les éléments dédiés
          document.getElementById('distance').innerText = `Distance : ${data.distance_km} km`;
          document.getElementById('duration').innerText = `Durée estimée : ${data.duration_min} min`;
        }
      })
      .catch(error => console.error('Erreur:', error));
  });

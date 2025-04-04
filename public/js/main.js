const ReservationApp = {
  apiKey: "",

  // 🔐 Chargement de la clé API depuis une variable globale (injectée dans la page)
  loadApiKey: async function () {
    this.apiKey = window.HERE_API_KEY || '';
  },

  // 🔍 Récupère les suggestions à partir de l’API HERE Autosuggest selon l’entrée utilisateur
  fetchSuggestions: async function (inputId) {
    const inputField = document.getElementById(inputId);
    const suggestionsListId = `${inputId}-suggestions`;
    let suggestionsList = document.getElementById(suggestionsListId);
    const query = inputField.value.trim();

    // ⚠️ Si la saisie contient moins de 3 caractères, on ne fait pas de requête
    if (query.length < 3) {
      if (suggestionsList) suggestionsList.innerHTML = "";
      return;
    }

    // 📡 Requête vers l’API HERE Autosuggest avec point d’ancrage sur Marseille
    const url = `https://autosuggest.search.hereapi.com/v1/autosuggest?q=${encodeURIComponent(query)}&at=43.2965,5.3698&apiKey=${this.apiKey}`;

    try {
      const response = await fetch(url);
      if (!response.ok) throw new Error(`Erreur HTTP : ${response.status}`);
      const data = await response.json();
      this.displaySuggestions(data.items, inputId); // 🖼️ Affiche les suggestions reçues
    } catch (error) {
      console.error("Erreur lors de la requête :", error);
    }
  },

  // 🖼️ Affiche dynamiquement les suggestions sous le champ saisi
  displaySuggestions: function (suggestions, inputId) {
    const inputField = document.getElementById(inputId);
    let suggestionsList = document.getElementById(`${inputId}-suggestions`);

    // 📦 Création du bloc suggestions s’il n’existe pas
    if (!suggestionsList) {
      suggestionsList = document.createElement("ul");
      suggestionsList.id = `${inputId}-suggestions`;
      suggestionsList.classList.add("autocomplete-list");
      inputField.parentNode.appendChild(suggestionsList);
    }

    suggestionsList.innerHTML = "";

    // ⭐ Option spéciale : utiliser ma position actuelle
    const useLocationItem = document.createElement("li");
    useLocationItem.innerHTML = `<span class="icon">📍</span><span>Utiliser ma position</span>`;
    useLocationItem.addEventListener("click", () => {
      this.useGeolocation(inputId);
      suggestionsList.innerHTML = "";
    });
    suggestionsList.appendChild(useLocationItem);

    // ❌ Aucun résultat
    if (!suggestions || suggestions.length === 0) {
      const emptyItem = document.createElement("li");
      emptyItem.textContent = "Aucune suggestion trouvée";
      suggestionsList.appendChild(emptyItem);
      return;
    }

    // ✅ Pour chaque suggestion, on crée un élément dans la liste avec une icône contextuelle
    suggestions.forEach(item => {
      const li = document.createElement("li");
      const label = item.address.label;
      let icon = "📍";

      // 🧠 Icônes personnalisées selon le type de lieu
      if (/gare/i.test(label)) icon = "🚄";
      else if (/aeroport|aéroport/i.test(label)) icon = "✈️";
      else if (/marseille/i.test(label)) icon = "🗺️";

      li.innerHTML = `<span class="icon">${icon}</span><span>${label}</span>`;
      li.addEventListener("click", () => {
        inputField.value = label;
        suggestionsList.innerHTML = "";
      });
      suggestionsList.appendChild(li);
    });
  },

  // ⚙️ Active l’autocomplétion sur un champ
  setupAutocomplete: function (inputId) {
    const inputField = document.getElementById(inputId);
    if (inputField) {
      inputField.setAttribute("autocomplete", "off");
      inputField.addEventListener("input", () => this.fetchSuggestions(inputId));
    }
  },

  // 📍 Prépare la géolocalisation (fonction de sécurité ou future logique)
  setupGeolocation: function (inputId) {
    const inputField = document.getElementById(inputId);
    if (!inputField) {
      console.warn(`Champ avec l'ID "${inputId}" introuvable pour la géolocalisation.`);
    }
  },

  // 📌 Utilise la position actuelle de l’utilisateur et remplit le champ avec l’adresse détectée
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
          if (!response.ok) throw new Error(`Erreur HTTP : ${response.status}`);
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

  // 🚀 Initialise l’application : charge la clé API et configure tous les champs d’entrée
  init: async function () {
    await this.loadApiKey();
    const inputIds = ['reservation_depart', 'reservation_arrivee', 'reservation_stopLieu'];
    inputIds.forEach(id => {
      this.setupAutocomplete(id);
      this.setupGeolocation(id);
    });
  }
};

// 🧹 Cache les listes de suggestions si l’utilisateur clique ailleurs
document.addEventListener("click", (event) => {
  document.querySelectorAll(".autocomplete-list").forEach(list => {
    if (!list.previousElementSibling.contains(event.target) && !list.contains(event.target)) {
      list.innerHTML = "";
    }
  });
});

// 🧬 Démarre l’application une fois que le DOM est chargé
document.addEventListener("DOMContentLoaded", () => {
  ReservationApp.init();
});

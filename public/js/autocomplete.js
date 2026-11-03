// ============================================================================
// 📂 autocomplete.js – Gestion des suggestions d'adresse avec HERE API
// ============================================================================
// Ce module encapsule toutes les fonctions liées à l’autocomplétion d’adresses,
// l’affichage des suggestions et l’utilisation de la géolocalisation.
// Il est attaché à window.Autocomplete pour être accessible globalement.
// ============================================================================

(function () {
  window.Autocomplete = {
    apiKey: "", // 🔑 Stocke la clé API HERE (initialisée via initApiKey)
    _debounceTimers: {}, // ⏱️ Un timer par champ, pour ne requêter qu'après une pause de saisie
    _abortControllers: {}, // 🚫 Permet d'annuler une requête devenue obsolète
    _lastDispatchedValue: {}, // 🔁 Évite de redéclencher un recalcul déjà fait (clic sur suggestion)

    // =========================================================================
    // ⚙️ Initialise la clé API HERE
    // =========================================================================
    initApiKey: function (key) {
      this.apiKey = key;
    },

    // =========================================================================
    // 🔍 Requête de suggestions HERE en fonction du texte saisi
    // -------------------------------------------------------------------------
    // inputId : ID du champ texte pour lequel on veut des suggestions
    // - Si moins de 3 caractères → efface les suggestions et stoppe
    // - Sinon → appelle l’API HERE Autosuggest et affiche les résultats
    // =========================================================================
    fetchSuggestions: async function (inputId) {
      const input = document.getElementById(inputId);
      const query = input.value.trim();
      const listId = `${inputId}-suggestions`;
      let list = document.getElementById(listId);

      // Pas de recherche si la saisie est trop courte
      if (query.length < 3) {
        if (list) list.innerHTML = "";
        return;
      }

      // Construction de l’URL de l’API HERE Autosuggest
      const url = `https://autosuggest.search.hereapi.com/v1/autosuggest?q=${encodeURIComponent(
        query
      )}&at=43.2965,5.3698&apiKey=${this.apiKey}`;

      // 🚫 Annule la requête précédente pour ce champ si elle est encore en vol
      // (évite qu'une réponse tardive et obsolète n'écrase des suggestions plus récentes)
      this._abortControllers[inputId]?.abort();
      const controller = new AbortController();
      this._abortControllers[inputId] = controller;

      try {
        const res = await fetch(url, { signal: controller.signal });
        if (!res.ok) throw new Error("Erreur API HERE");
        const data = await res.json();

        // Affichage des suggestions récupérées
        this.displaySuggestions(data.items, inputId);
      } catch (err) {
        if (err.name === "AbortError") return;
        console.error("Erreur lors de la récupération des suggestions :", err);
      }
    },

    // =========================================================================
    // ⏳ debouncedFetchSuggestions – attend une pause de 300ms dans la saisie
    // avant d'appeler l'API HERE, pour ne pas consommer un token par frappe.
    // =========================================================================
    debouncedFetchSuggestions: function (inputId) {
      clearTimeout(this._debounceTimers[inputId]);
      this._debounceTimers[inputId] = setTimeout(() => {
        this.fetchSuggestions(inputId);
      }, 300);
    },

    // =========================================================================
    // 🖼️ Affiche les suggestions sous le champ concerné
    // -------------------------------------------------------------------------
    // items   : liste de résultats renvoyés par l’API HERE
    // inputId : ID du champ texte auquel associer les suggestions
    // =========================================================================
    displaySuggestions: function (items, inputId) {
      const input = document.getElementById(inputId);
      let list = document.getElementById(`${inputId}-suggestions`);

      // Création de la liste UL si elle n’existe pas encore
      if (!list) {
        list = document.createElement("ul");
        list.id = `${inputId}-suggestions`;
        list.classList.add("autocomplete-list"); // Classe CSS pour le style
        input.parentNode.appendChild(list);
      }

      // Nettoyage de la liste avant d’ajouter de nouveaux éléments
      list.innerHTML = "";

      // Boucle sur chaque suggestion de l’API
      items.forEach((item) => {
        const li = document.createElement("li");

        // Texte affiché = adresse complète (label) ou titre
        li.textContent = item.address?.label || item.title;

        // Quand l’utilisateur clique sur une suggestion :
        // - Remplir l’input avec le texte choisi
        // - Effacer la liste de suggestions
        // - Déclencher un événement personnalisé "addressUpdated"
        li.addEventListener("click", () => {
          input.value = li.textContent;
          list.innerHTML = "";
          this._lastDispatchedValue[inputId] = input.value;

          // 🔁 Notifie les autres modules (ex: la carte) qu’une adresse a changé
          const event = new CustomEvent("addressUpdated", {
            detail: { inputId },
          });
          input.dispatchEvent(event);
        });

        list.appendChild(li);
      });
    },

    // =========================================================================
    // ⌨️ Attache l’autocomplétion à un champ donné
    // -------------------------------------------------------------------------
    // id : ID du champ input texte
    // Ajoute un écouteur "input" pour déclencher fetchSuggestions()
    // =========================================================================
    setupAutocomplete: function (id) {
      const input = document.getElementById(id);
      if (input) {
        input.addEventListener("input", () => this.debouncedFetchSuggestions(id));

        // 🧭 Si l'utilisateur tape/colle une adresse complète sans cliquer sur
        // une suggestion (puis quitte le champ), on recalcule quand même le
        // trajet avec le texte saisi — sinon le km sous la carte reste figé.
        input.addEventListener("blur", () => {
          setTimeout(() => {
            const list = document.getElementById(`${id}-suggestions`);
            if (list) list.innerHTML = "";
            const value = input.value.trim();
            if (value && value !== this._lastDispatchedValue[id]) {
              this._lastDispatchedValue[id] = value;
              input.dispatchEvent(new CustomEvent("addressUpdated", { detail: { inputId: id } }));
            }
          }, 150);
        });
      }
    },

    // =========================================================================
    // 🚀 Initialise l’autocomplétion sur plusieurs champs
    // -------------------------------------------------------------------------
    // fields : tableau d’IDs de champs input à gérer
    // =========================================================================
    init: function (fields) {
      fields.forEach(this.setupAutocomplete.bind(this));
    },

    // =========================================================================
    // 📍 Utilise la géolocalisation pour remplir un champ avec l’adresse actuelle
    // -------------------------------------------------------------------------
    // inputId : ID du champ à remplir avec l’adresse courante
    // - Utilise navigator.geolocation pour obtenir latitude/longitude
    // - Fait un appel à l’API HERE Reverse Geocoding
    // - Remplit le champ avec l’adresse complète
    // - Déclenche l’événement "addressUpdated"
    // =========================================================================
    useGeolocation: function (inputId) {
      if (!navigator.geolocation) return;

      navigator.geolocation.getCurrentPosition(async (pos) => {
        const { latitude: lat, longitude: lng } = pos.coords;

        // URL de l’API HERE Reverse Geocode
        const url = `https://revgeocode.search.hereapi.com/v1/revgeocode?at=${lat},${lng}&apiKey=${this.apiKey}`;

        try {
          const res = await fetch(url);
          const data = await res.json();
          const label = data.items[0]?.address?.label;

          if (label) {
            const input = document.getElementById(inputId);
            input.value = label;
            this._lastDispatchedValue[inputId] = label;

            // 🔁 Notifie les autres modules qu’une adresse a changé
            const event = new CustomEvent("addressUpdated", {
              detail: { inputId },
            });
            input.dispatchEvent(event);
          }
        } catch (e) {
          console.error("Erreur lors de la géolocalisation :", e);
        }
      });
    },
  };
})();

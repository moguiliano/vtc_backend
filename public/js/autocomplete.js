// autocomplete.js – Gestion des suggestions d'adresse avec HERE API
(function () {
  window.Autocomplete = {
    apiKey: "",

    initApiKey: function (key) {
      this.apiKey = key;
    },

    // 🔍 Requête de suggestions HERE en fonction du champ saisi
    fetchSuggestions: async function (inputId) {
      const input = document.getElementById(inputId);
      const query = input.value.trim();
      const listId = `${inputId}-suggestions`;
      let list = document.getElementById(listId);

      if (query.length < 3) {
        if (list) list.innerHTML = "";
        return;
      }

      const url = `https://autosuggest.search.hereapi.com/v1/autosuggest?q=${encodeURIComponent(query)}&at=43.2965,5.3698&apiKey=${this.apiKey}`;

      try {
        const res = await fetch(url);
        if (!res.ok) throw new Error("Erreur API HERE");
        const data = await res.json();
        this.displaySuggestions(data.items, inputId);
      } catch (err) {
        console.error("Erreur lors de la récupération des suggestions :", err);
      }
    },

    // 🖼️ Affiche les suggestions sous le champ concerné
    displaySuggestions: function (items, inputId) {
      const input = document.getElementById(inputId);
      let list = document.getElementById(`${inputId}-suggestions`);

      if (!list) {
        list = document.createElement("ul");
        list.id = `${inputId}-suggestions`;
        list.classList.add("autocomplete-list");
        input.parentNode.appendChild(list);
      }

      list.innerHTML = "";

      items.forEach(item => {
        const li = document.createElement("li");
        li.textContent = item.address?.label || item.title;
        li.addEventListener("click", () => {
          input.value = li.textContent;
          list.innerHTML = "";
          // 🔁 Déclenche un événement personnalisé pour notifier la carte
          const event = new CustomEvent("addressUpdated", { detail: { inputId } });
          input.dispatchEvent(event);
        });
        list.appendChild(li);
      });
    },

    setupAutocomplete: function (id) {
      const input = document.getElementById(id);
      if (input) {
        input.addEventListener("input", () => this.fetchSuggestions(id));
      }
    },

    init: function (fields) {
      fields.forEach(this.setupAutocomplete.bind(this));
    },

    // 📍 Utilise la géolocalisation pour remplir un champ avec l'adresse actuelle
    useGeolocation: function (inputId) {
      if (!navigator.geolocation) return;

      navigator.geolocation.getCurrentPosition(async pos => {
        const { latitude: lat, longitude: lng } = pos.coords;
        const url = `https://revgeocode.search.hereapi.com/v1/revgeocode?at=${lat},${lng}&apikey=${this.apiKey}`;
        try {
          const res = await fetch(url);
          const data = await res.json();
          const label = data.items[0]?.address?.label;
          if (label) {
            const input = document.getElementById(inputId);
            input.value = label;
            // 🔁 Déclenche l'événement personnalisé après mise à jour du champ
            const event = new CustomEvent("addressUpdated", { detail: { inputId } });
            input.dispatchEvent(event);
          }
        } catch (e) {
          console.error("Erreur lors de la géolocalisation :", e);
        }
      });
    }
  };
})();

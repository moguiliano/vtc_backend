
// ui.js – Fonctions pour gérer l'affichage et les boutons de l'interface
(function () {
  window.UI = {
    // Active ou désactive le champ "Arrêt" en fonction de la checkbox
    setupStopCheckbox: function () {
      const checkbox = document.getElementById("reservation_Stop");
      const field = document.getElementById("stopover-location");
      if (checkbox && field) {
        checkbox.addEventListener("change", () => {
          field.style.display = checkbox.checked ? "block" : "none";
        });
        field.style.display = checkbox.checked ? "block" : "none";
      }
    },

    // Gère la sélection visuelle du véhicule et stocke le type choisi
    setupVehicleSelection: function () {
      document.querySelectorAll('.vehicle-card').forEach(card => {
        card.addEventListener('click', () => {
          document.querySelectorAll('.vehicle-card').forEach(c => c.classList.remove('active'));
          card.classList.add('active');
          document.getElementById('reservation_typeVehicule').value = card.dataset.type;
        });
      });
    },

    // Active les boutons "immédiat" ou "plus tard" pour afficher la date/heure
    setupTimeChoiceButtons: function () {
      const btnNow = document.getElementById('immediateBtn');
      const btnLater = document.getElementById('laterBtn');
      const group = document.getElementById('dateTimeGroup');
      const orange = '#ff5630', gray = 'gray';

      if (btnNow && btnLater && group) {
        btnNow.addEventListener('click', () => {
          btnNow.classList.add('active');
          btnLater.classList.remove('active');
          btnNow.style.backgroundColor = orange;
          btnLater.style.backgroundColor = gray;
          group.style.display = 'none';
        });

        btnLater.addEventListener('click', () => {
          btnLater.classList.add('active');
          btnNow.classList.remove('active');
          btnLater.style.backgroundColor = orange;
          btnNow.style.backgroundColor = gray;
          group.style.display = 'flex';
        });

        // Activation initiale
        btnNow.click();
      }
    },

    // Active l'onglet demandé et désactive les autres
    switchToTab: function (id) {
      document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
      document.querySelectorAll('.tab-button').forEach(b => {
        b.classList.remove('active');
        b.disabled = true;
      });
      const btn = document.querySelector(`.tab-button[data-tab="${id}"]`);
      const tab = document.getElementById(id);
      if (btn && tab) {
        btn.classList.add('active');
        btn.disabled = false;
        tab.classList.add('active');
      }
    },

    // Permet d'activer un onglet cliquable (visuellement)
    unlockTab: function (index) {
      const tab = document.querySelectorAll('.tab-button')[index];
      if (tab) {
        tab.disabled = false;
        tab.style.pointerEvents = 'auto';
        tab.style.opacity = '1';
      }
    }
  };
})();

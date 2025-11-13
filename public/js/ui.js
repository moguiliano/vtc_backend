// ============================================================================
// 📂 ui.js – Fonctions pour gérer l'affichage et les boutons de l'interface
// ============================================================================
// Ce module gère la logique d’affichage dynamique dans le formulaire de
// réservation : affichage des arrêts, sélection du véhicule, choix du temps,
// navigation entre onglets, etc.
// ============================================================================

(function () {
  window.UI = {
    // =========================================================================
    // ⛔ Active ou désactive le champ "Arrêt" en fonction de la checkbox
    // -------------------------------------------------------------------------
    // - Surveille la case à cocher "reservation_Stop"
    // - Si cochée → affiche le champ "stopover-location"
    // - Si décochée → cache ce champ
    // =========================================================================
    setupStopCheckbox: function () {
      const checkbox = document.getElementById("reservation_Stop");
      const field = document.getElementById("stopover-location");

      if (checkbox && field) {
        // Ajoute un listener sur la checkbox
        checkbox.addEventListener("change", () => {
          field.style.display = checkbox.checked ? "block" : "none";
        });

        // Applique l’état initial (au chargement de la page)
        field.style.display = checkbox.checked ? "block" : "none";
      }
    },

    // =========================================================================
    // 🚘 Gère la sélection visuelle du véhicule et stocke le type choisi
    // -------------------------------------------------------------------------
    // - Chaque carte de véhicule (".vehicle-card") est cliquable
    // - Au clic :
    //   1. Désélectionne toutes les cartes
    //   2. Active la carte cliquée
    //   3. Met à jour un champ caché "reservation_typeVehicule"
    //   4. Met à jour un champ caché "reservation_prix" avec le prix affiché
    // =========================================================================
    setupVehicleSelection: function () {
      // Sélectionne toutes les cartes de véhicule
      document.querySelectorAll('.vehicle-card').forEach(card => {
    
        // Ajoute un événement "click" à chaque carte
        card.addEventListener('click', () => {
    
          // 1. Retire la classe "active" de toutes les cartes (désélection)
          document.querySelectorAll('.vehicle-card').forEach(c => c.classList.remove('active'));
    
          // 2. Ajoute la classe "active" à la carte cliquée
          card.classList.add('active');
    
          // 3. Met à jour le champ caché avec le type du véhicule sélectionné
          document.getElementById('reservation_typeVehicule').value = card.dataset.type;
    
          // 4. Récupère l'élément qui contient le prix dans la carte active
          const priceElement = card.querySelector('.vehicle-price');
    
          if (priceElement) {
            // 5. Extrait le prix depuis le texte (ex : "Prix : 63 €" → 63)
            const prix = priceElement.textContent.match(/[\d.]+/)?.[0];
    
            if (prix) {
              // 6. Met à jour le champ caché avec le prix (ex : <input id="reservation_prix" ...>)
              document.getElementById('reservation_prix').value = prix;
            }
          }
        });
      });
    },
    
    // =========================================================================
    // ⏱️ Active les boutons "Immédiat" ou "Plus tard"
    // -------------------------------------------------------------------------
    // - "Immédiat" → cache les champs date/heure
    // - "Plus tard" → affiche les champs date/heure
    // - Applique une couleur orange au bouton actif et grise à l’autre
    // =========================================================================
    setupTimeChoiceButtons: function () {
      const btnNow = document.getElementById('immediateBtn');
      const btnLater = document.getElementById('laterBtn');
      const group = document.getElementById('dateTimeGroup');

      // 🎨 Couleurs utilisées
      const orange = '#ff5630', gray = 'gray';

      if (btnNow && btnLater && group) {
        // Clic sur "Immédiat"
        btnNow.addEventListener('click', () => {
          btnNow.classList.add('active');
          btnLater.classList.remove('active');
          btnNow.style.backgroundColor = orange;
          btnLater.style.backgroundColor = gray;
          group.style.display = 'none'; // Cache la sélection date/heure
        });

        // Clic sur "Plus tard"
        btnLater.addEventListener('click', () => {
          btnLater.classList.add('active');
          btnNow.classList.remove('active');
          btnLater.style.backgroundColor = orange;
          btnNow.style.backgroundColor = gray;
          group.style.display = 'flex'; // Affiche la sélection date/heure
        });

        // ✅ Par défaut, on active "Immédiat"
        btnNow.click();
      }
    },

    // =========================================================================
    // 🗂️ Active l'onglet demandé et désactive les autres
    // -------------------------------------------------------------------------
    // id : identifiant de l’onglet à activer
    // - Supprime "active" de tous les onglets
    // - Active uniquement l’onglet demandé
    // =========================================================================
    switchToTab: function (id) {
      // Désactive tous les contenus et boutons d’onglets
      document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
      document.querySelectorAll('.tab-button').forEach(b => {
        b.classList.remove('active');
        b.disabled = true;
      });

      // Active uniquement l’onglet demandé
      const btn = document.querySelector(`.tab-button[data-tab="${id}"]`);
      const tab = document.getElementById(id);

      if (btn && tab) {
        btn.classList.add('active');
        btn.disabled = false;
        tab.classList.add('active');
      }
    },

    // =========================================================================
    // 🔓 Permet d'activer un onglet cliquable (visuellement)
    // -------------------------------------------------------------------------
    // index : position de l’onglet dans la liste des boutons
    // - Réactive le bouton (disabled → false)
    // - Autorise le clic avec pointer-events
    // - Restaure l’opacité
    // =========================================================================
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

// ============================================================================
// 📌 NOTE : disparition des boutons "appel" et "WhatsApp" lorsqu'ils se
//           superposent avec le formulaire de réservation.
// ---------------------------------------------------------------------------
// 👉 Cette remarque suggère que tu prévois d’ajouter une fonction supplémentaire
//    ici pour gérer l’affichage/masquage de ces boutons. 
//    Actuellement, le code n’est pas encore implémenté.
// ============================================================================

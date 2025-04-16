
// reservation.js – Logique liée à la réservation du trajet et calculs associés
(function () {
  window.Reservation = {
    // Initialise les événements liés à la réservation
    setupReservationHandlers: function () {
      const goToVehicleBtn = document.getElementById('goToVehicle');
      const nextBtn = document.getElementById('nextRecapBtn');

      // Étape 2 – Validation des champs, appel API backend et affichage récapitulatif
      if (goToVehicleBtn) {
        goToVehicleBtn.addEventListener('click', async () => {
          const pickup = document.getElementById('reservation_depart')?.value.trim();
          const dropoff = document.getElementById('reservation_arrivee')?.value.trim();
          const stop = document.getElementById('reservation_stopLieu')?.value.trim();
          const stopEnabled = document.getElementById('reservation_Stop')?.checked;
          const heure = new Date().getHours();

          if (!pickup || !dropoff || (stopEnabled && !stop)) {
            alert("Merci de remplir tous les champs requis avant de continuer.");
            return;
          }

          // Envoi au backend pour calculer distance/durée/prix
          const res = await fetch('/reservation/calculate-trip', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ pickup, dropoff, stopEnabled, stop, heure })
          });

          const data = await res.json();

          if (data.distance_km && data.duration_min) {
            document.getElementById("recapitulatif-trajet").innerHTML = `
              <div><strong>Distance estimée :</strong> ${data.distance_km} km</div>
              <div><strong>Durée estimée :</strong> ${data.duration_min} minutes</div>
            `;

            // Affichage du prix par type de véhicule
            const recapPrixBlock = document.getElementById("recap_prix_list");
            if (recapPrixBlock && data.prix) {
              recapPrixBlock.innerHTML = '';
              Object.entries(data.prix).forEach(([cat, prix]) => {
                const label = {
                  eco_berline: "Eco-Berline",
                  grand_coffre: "Grand Coffre",
                  berline: "Berline",
                  van: "Van"
                }[cat] || cat;

                recapPrixBlock.innerHTML += `
                  <div><strong>${label} :</strong> ${prix.prix_total} €${prix.majoration_nuit ? ' (nuit)' : ''}</div>
                `;
              });
            }

            // Passage à l'étape suivante
            window.UI.unlockTab(1);
            window.UI.switchToTab('tab2');
          } else {
            console.error("Réponse incomplète", data);
          }
        });
      }

      // Étape 3 – Résumé de la réservation sélectionnée
      if (nextBtn) {
        nextBtn.addEventListener('click', () => {
          const type = document.getElementById('reservation_typeVehicule')?.value;
          if (!type) return alert("Merci de sélectionner un véhicule.");

          // Injection des données dans le récapitulatif
          document.getElementById("recap_depart").textContent = document.getElementById("reservation_depart").value;
          document.getElementById("recap_arrivee").textContent = document.getElementById("reservation_arrivee").value;
          document.getElementById("recap_stop").textContent = document.getElementById("reservation_stopLieu").value || "Aucun";
          document.getElementById("recap_siege").textContent = document.getElementById("reservation_siegeBebe").checked ? "Oui" : "Non";
          document.getElementById("recap_type").textContent = type;

          window.UI.unlockTab(2);
          window.UI.switchToTab('tab3');
        });
      }
    }
  };
})();

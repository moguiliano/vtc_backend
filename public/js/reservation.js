// ============================================================================
// 📂 reservation.js – Logique liée à la réservation du trajet et calculs associés
// ============================================================================
// Ce module gère :
//  - les transitions entre les étapes du formulaire de réservation,
//  - l’appel au backend pour calculer distance/durée/prix,
//  - la mise à jour du récapitulatif et des champs cachés,
//  - l’injection d’infos sous la carte et dans les différents onglets.
// Il expose un objet global window.Reservation avec une méthode d’initialisation.
// ============================================================================

(function () {
  window.Reservation = {
    // =========================================================================
    // 🚀 Initialise les événements liés à la réservation
    // -------------------------------------------------------------------------
    // - Bouton "goToVehicle" (passage à l’étape 2 + calcul trajet/prix)
    // - Bouton "nextRecapBtn" (passage à l’étape 3 + récap final)
    // =========================================================================
    setupReservationHandlers: function () {
      const goToVehicleBtn = document.getElementById("goToVehicle");
      const nextBtn = document.getElementById("nextRecapBtn");

      /**
       * 🧭 Affiche distance et durée sous la carte
       * -----------------------------------------------------------------------
       * - Lit les infos globales (window.TrajetInfos) si disponibles
       * - Met à jour les spans #distanceText et #durationText
       * - Met à jour les inputs hidden #reservation_distance et #reservation_duree
       */
      function afficherInfosCarte() {
        if (!window.TrajetInfos) return;

        const { distance, duree } = window.TrajetInfos;

        const distanceSpan = document.getElementById("distanceText");
        const durationSpan = document.getElementById("durationText");

        if (distanceSpan)
          distanceSpan.textContent = `${distance.toFixed(2)} km`;
        if (durationSpan)
          durationSpan.textContent = `${duree.toFixed(1)} minutes`;

        // 🆕 Remplir les champs hidden du formulaire dans l'onglet 1
        const inputDistance = document.getElementById("reservation_distance");
        const inputDuree = document.getElementById("reservation_duree");

        if (inputDistance) inputDistance.value = distance.toFixed(2);
        if (inputDuree) inputDuree.value = duree.toFixed(1);
      }

      // =========================================================================
      // ▶️ Étape 2 – Validation des champs et appel au backend
      // -------------------------------------------------------------------------
      // - Vérifie que départ/arrivée/arrêt (si activé) sont remplis
      // - Appelle /reservation/calculate-trip (POST) avec pickup/dropoff/stop/heure
      // - Met à jour :
      //    • window.TrajetInfos (distance/durée)
      //    • les champs hidden (distance/durée)
      //    • le récap visuel (#recapitulatif-trajet)
      //    • les prix par type de véhicule (.vehicle-price)
      //    • le bloc récapitulatif des prix (#recap_prix_list)
      // - Passe à l’onglet 2
      // =========================================================================
      if (goToVehicleBtn) {
        goToVehicleBtn.addEventListener("click", async () => {
          // Récupération des champs de l’onglet 1
          const pickup = document
            .getElementById("reservation_depart")
            ?.value.trim();
          const dropoff = document
            .getElementById("reservation_arrivee")
            ?.value.trim();
          const stop = document
            .getElementById("reservation_stopLieu")
            ?.value.trim();
          const stopEnabled =
            document.getElementById("reservation_Stop")?.checked;
          const heure = new Date().getHours(); // Heure courante (utile pour majoration nuit)

          // ⚠️ Validation minimale des champs requis
          if (!pickup || !dropoff || (stopEnabled && !stop)) {
            alert(
              "Merci de remplir tous les champs requis avant de continuer."
            );
            return;
          }

          try {
            // Appel au backend pour calculer le trajet et les tarifs
            const res = await fetch("/reservation/calculate-trip", {
              method: "POST",
              headers: { "Content-Type": "application/json" },
              body: JSON.stringify({
                pickup,
                dropoff,
                stopEnabled,
                stop,
                heure,
              }),
            });

            const data = await res.json();

            // ✅ Vérifie que la réponse contient bien distance et durée
            if (data.distance_km && data.duration_min) {
              // 📦 Stockage global pour réutilisation (ex : affichage sous la carte)
              window.TrajetInfos = {
                distance: data.distance_km,
                duree: data.duration_min,
              };

              // 📝 Remplissage des champs cachés pour soumission finale
              document.getElementById("reservation_duree").value =
                data.duration_min.toFixed(1);
              document.getElementById("reservation_distance").value =
                data.distance_km.toFixed(2);

              // 🖼️ Récapitulatif visuel (distance/durée)
              document.getElementById("recapitulatif-trajet").innerHTML = `
                <div><strong>Distance estimée :</strong> ${data.distance_km} km</div>
                <div><strong>Durée estimée :</strong> ${data.duration_min} minutes</div>
              `;

              // ➕ Mise à jour des infos sous la carte (spans + inputs hidden)
              afficherInfosCarte();

              // 💶 Affichage des prix par type de véhicule dans les cartes
              const prixVehicules = data.prix;
              document.querySelectorAll(".vehicle-price").forEach((el) => {
                const type = el.dataset.type;
                if (prixVehicules[type]?.prix_total) {
                  el.textContent = `Prix : ${prixVehicules[
                    type
                  ].prix_total.toFixed(2)} €`;
                } else {
                  el.textContent = "Prix indisponible";
                }
              });

              // 🧾 Bloc récapitulatif des prix (liste en dessous)
              const recapPrixBlock = document.getElementById("recap_prix_list");
              if (recapPrixBlock) {
                recapPrixBlock.innerHTML = "";
                Object.entries(data.prix || {}).forEach(([cat, prix]) => {
                  // Libellés lisibles pour l’utilisateur
                  const label =
                    {
                      eco_berline: "Eco-Berline",
                      grand_coffre: "Grand Coffre",
                      berline: "Berline",
                      van: "Van",
                    }[cat] || cat;

                  recapPrixBlock.innerHTML += `
                    <div><strong>${label} :</strong> ${prix.prix_total} €${
                    prix.majoration_nuit ? " (nuit)" : ""
                  }</div>
                  `;
                });
              }

              // 🔄 Passage à l'étape suivante (onglet 2)
              window.UI.unlockTab(1);
              window.UI.switchToTab("tab2");
            } else {
              console.error("Réponse incomplète ou invalide :", data);
            }
          } catch (error) {
            // Journalise l’erreur réseau/serveur pour diagnostic
            console.error("Erreur lors du calcul de trajet :", error);
          }
        });
      }

      // =========================================================================
      // ▶️ Étape 3 – Résumé final de la réservation
      // -------------------------------------------------------------------------
      // - Vérifie qu’un véhicule est sélectionné
      // - Récupère les infos des champs et les insère dans le récap final
      // - Passe à l’onglet 3
      // =========================================================================
      if (nextBtn) {
        nextBtn.addEventListener("click", () => {
          const type = document.getElementById(
            "reservation_typeVehicule"
          )?.value;

          // ⚠️ Empêche d’aller au récap sans avoir choisi un véhicule
          if (!type) {
            alert("Merci de sélectionner un véhicule.");
            return;
          }

          // 🧾 Injection des infos dans le récapitulatif final (onglet 3)
          document.getElementById("recap_depart").textContent =
            document.getElementById("reservation_depart").value;
          document.getElementById("recap_arrivee").textContent =
            document.getElementById("reservation_arrivee").value;
          document.getElementById("recap_stop").textContent =
            document.getElementById("reservation_stopLieu").value || "Aucun";
          document.getElementById("recap_siege").textContent =
            document.getElementById("reservation_siegeBebe").checked
              ? "Oui"
              : "Non";
          document.getElementById("recap_type").textContent = type;

          // 🔄 Passage à l'onglet final (onglet 3)
          window.UI.unlockTab(2);
          window.UI.switchToTab("tab3");
        });
      }
    },
  };
})();

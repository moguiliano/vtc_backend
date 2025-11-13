// ============================================================================
// 📂 map.js – Initialisation optimisée de la carte HERE avec tracé GPS et centrage intelligent
// ============================================================================
// Ce module encapsule toute la logique liée à la carte HERE :
//  - initialisation de la plateforme et de la carte,
//  - gestion responsive (resize, observer d'onglet),
//  - création/maj de marqueurs (Départ, Arrivée, Arrêt),
//  - géocodage d’une adresse → coordonnées,
//  - calcul d’itinéraire et tracé sur la carte,
//  - recentrage automatique avec marges adaptatives,
//  - synchronisation des infos (distance/durée) avec l’UI et des champs hidden.
// Expose window.ZenMap.initMap(apiKey?) pour l’initialiser.
// ============================================================================

(function () {
  window.ZenMap = {
    // =========================================================================
    // 🚀 initMap(apiKey?) – Point d’entrée d’initialisation de la carte
    // -------------------------------------------------------------------------
    // - apiKey (optionnel) : permet de passer une clé à l’exécution.
    // - Si non fournie, on utilise window.HERE_API_KEY en fallback.
    // - Monte la carte, attache le resize, et prépare les écouteurs d’inputs.
    // - Retourne un objet avec destroy() pour nettoyer les écouteurs.
    // =========================================================================
    initMap: function (apiKey = null) {
      const markers = {}; // Stockage des marqueurs par type (Départ, Arrivée, Arrêt)
      const allPoints = {}; // Coordonnées associées à chaque type
      let routeLine; // Ligne de trajet affichée sur la carte

      // ✅ Initialisation de la plateforme HERE AVEC clé sécurisée (passée en paramètre OU depuis window si fallback nécessaire)
      const effectiveApiKey = apiKey || window.HERE_API_KEY;
      const platform = new H.service.Platform({ apikey: apiKey || window.HERE_API_KEY });

      // 🗺️ Récupération du conteneur de carte
      const mapContainer = document.getElementById("mapContainer");
      if (!mapContainer) return console.warn("🛑 Aucun container de carte trouvé");

      // 🧱 Couches par défaut + création de la carte
      const defaultLayers = platform.createDefaultLayers();
      const map = new H.Map(mapContainer, defaultLayers.vector.normal.map, {
        zoom: 11,
        center: { lat: 43.2965, lng: 5.3698 }, // Marseille par défaut
      });

      // 🖱️ Activation du zoom/pan + UI par défaut
      new H.mapevents.Behavior(new H.mapevents.MapEvents(map));
      const ui = H.ui.UI.createDefault(map, defaultLayers);

      // -----------------------------------------------------------------------
      // 📐 getResponsivePadding() – marges adaptatives pour le recentrage
      // - Sur mobile : marges fixes réduites
      // - Sinon : marges proportionnelles à la taille du viewport
      // -----------------------------------------------------------------------
      function getResponsivePadding() {
        if (window.innerWidth <= 767) {
          return { top: 20, left: 20, bottom: 20, right: 20 };
        }
        const vp = map.getViewPort();
        return vp.getHeight && vp.getWidth
          ? {
              top: vp.getHeight() * 0.15,
              left: vp.getWidth() * 0.15,
              bottom: vp.getHeight() * 0.15,
              right: vp.getWidth() * 0.15,
            }
          : { top: 50, left: 50, bottom: 50, right: 50 };
      }

      // -----------------------------------------------------------------------
      // 🔄 resizeMap() – force le resize et occupe 100% du container
      // - Appelle map.getViewPort().resize() (deux fois pour fiabiliser)
      // -----------------------------------------------------------------------
      function resizeMap() {
        const container = document.getElementById("mapContainer");
        if (container && map && map.getViewPort()) {
          container.style.width = "100%";
          container.style.height = "100%";
          map.getViewPort().resize();
          setTimeout(() => map.getViewPort().resize(), 150);
        }
      }

      // -----------------------------------------------------------------------
      // ⏳ delayedResize() – couple resize immédiat + retardé (relayout tardif)
      // -----------------------------------------------------------------------
      function delayedResize() {
        resizeMap();
        setTimeout(resizeMap, 1500);
      }

      // 🔊 Resize fenêtre → resize carte
      window.addEventListener("resize", resizeMap);

      // 👀 MutationObserver : quand #tab1 devient .active → resize carte
      const tabContent = document.querySelector("#tab1");
      const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
          if (
            mutation.type === "attributes" &&
            mutation.attributeName === "class" &&
            mutation.target.classList.contains("active")
          ) {
            setTimeout(resizeMap, 200);
          }
        });
      });
      if (tabContent) {
        observer.observe(tabContent, {
          attributes: true,
          attributeFilter: ["class"],
        });
      }

      // ⏱️ Kickstart : petit délai pour laisser le DOM se stabiliser
      setTimeout(() => {
        console.log("🚀 Initialisation du redimensionnement...");
        delayedResize();
        console.log("🏁 Initialisation terminée");
      }, 800);

      // -----------------------------------------------------------------------
      // 🏷️ getIcon(label) – génère une icône SVG personnalisée par type
      // - Trois libellés gérés : Départ, Arrivée, Arrêt
      // - Retourne un H.map.Icon
      // -----------------------------------------------------------------------
      function getIcon(label) {
        const icons = {
          Départ: `<svg xmlns='http://www.w3.org/2000/svg' width='90' height='30'>
               <rect x='0' y='0' width='90' height='30' rx='5' fill='#666'/>
               <text x='45' y='20' font-size='14' font-family='Inter, sans-serif' text-anchor='middle' fill='white'>Départ</text>
             </svg>`,
          Arrivée: `<svg xmlns='http://www.w3.org/2000/svg' width='90' height='30'>
                <rect x='0' y='0' width='90' height='30' rx='5' fill='#666'/>
                <text x='45' y='20' font-size='14' font-family='Inter, sans-serif' text-anchor='middle' fill='white'>Arrivée</text>
             </svg>`,
          Arrêt: `<svg xmlns='http://www.w3.org/2000/svg' width='90' height='30'>
              <rect x='0' y='0' width='90' height='30' rx='5' fill='#666'/>
              <text x='45' y='20' font-size='14' font-family='Inter, sans-serif' text-anchor='middle' fill='white'>Arrêt</text>
           </svg>`
        };
        return new H.map.Icon(icons[label] || icons["Arrêt"]);
      }

      // -----------------------------------------------------------------------
      // 📍 addMarker(coords, label) – ajoute/remplace un marqueur typé
      // - Si un marqueur du même label existe, on le retire avant d’ajouter
      // - Stocke aussi la position dans allPoints[label]
      // -----------------------------------------------------------------------
      function addMarker(coords, label) {
        if (markers[label]) map.removeObject(markers[label]);
        const marker = new H.map.Marker(coords, { icon: getIcon(label) });
        map.addObject(marker);
        markers[label] = marker;
        allPoints[label] = coords;
      }

      // -----------------------------------------------------------------------
      // 🔁 updateTrajetInfosUI() – pousse distance/durée dans l’UI + hidden
      // - Lit window.TrajetInfos (si défini par le routing)
      // - Met à jour #distanceText, #durationText et les inputs hidden
      // -----------------------------------------------------------------------
      function updateTrajetInfosUI() {
        if (!window.TrajetInfos) return;
        const { distance, duree } = window.TrajetInfos;
        const dt = document.getElementById("distanceText");
        const tt = document.getElementById("durationText");
        const di = document.getElementById("reservation_distance");
        const ti = document.getElementById("reservation_duree");

        if (dt) dt.textContent = `${distance.toFixed(2)} km`;
        if (tt) tt.textContent = `${duree.toFixed(1)} minutes`;
        if (di) di.value = distance.toFixed(2);
        if (ti) ti.value = duree.toFixed(1);
      }

      // -----------------------------------------------------------------------
      // 🎯 centerMapOnPoints() – recentre la vue pour contenir tous les points
      // - Calcule une bounding box (H.geo.Rect) sur l’ensemble des points
      // - Applique un padding responsive
      // -----------------------------------------------------------------------
      function centerMapOnPoints() {
        const coords = Object.values(allPoints);
        if (!coords.length) return;

        const bbox = new H.geo.Rect(
          Math.min(...coords.map((p) => p.lat)),
          Math.min(...coords.map((p) => p.lng)),
          Math.max(...coords.map((p) => p.lat)),
          Math.max(...coords.map((p) => p.lng))
        );

        map.getViewModel().setLookAtData({
          bounds: bbox,
          animate: true,
          padding: getResponsivePadding(),
        });
      }

      // -----------------------------------------------------------------------
      // 🧭 geocode(adresse, callback) – transforme une adresse en coordonnées
      // - Utilise l’API HERE Geocoding & Search (v1)
      // - En cas de résultat, appelle callback({lat, lng})
      // -----------------------------------------------------------------------
      async function geocode(adresse, callback) {
        const encoded = encodeURIComponent(adresse);
        const url = `https://geocode.search.hereapi.com/v1/geocode?q=${encoded}&apikey=${effectiveApiKey}`;
        try {
          const res = await fetch(url);
          const data = await res.json();
          const pos = data.items[0]?.position;
          if (pos) callback({ lat: pos.lat, lng: pos.lng });
        } catch (e) {
          console.error("Erreur de géocodage :", e);
        }
      }

      // -----------------------------------------------------------------------
      // 🛣️ updateRouteLine() – calcule et trace l’itinéraire
      // - Construit la liste des waypoints (Départ → [Arrêt] → Arrivée)
      // - Appelle Router v8 (car) avec retour polyline + summary
      // - Construit un LineString à partir des sections renvoyées
      // - Dessine la polyline sur la carte, puis recentre
      // - Met à jour window.TrajetInfos (distance km / durée min) + UI
      // -----------------------------------------------------------------------
      async function updateRouteLine() {
        if (routeLine) map.removeObject(routeLine);

        const waypoints = ["Départ", "Arrêt", "Arrivée"]
          .map((l) => allPoints[l])
          .filter(Boolean);
        if (waypoints.length < 2) return;

        const url = new URL("https://router.hereapi.com/v8/routes");
        url.searchParams.append("transportMode", "car");
        url.searchParams.append("origin", `${waypoints[0].lat},${waypoints[0].lng}`);
        url.searchParams.append("destination", `${waypoints.at(-1).lat},${waypoints.at(-1).lng}`);
        if (waypoints.length === 3) {
          url.searchParams.append("via", `${waypoints[1].lat},${waypoints[1].lng}`);
        }
        url.searchParams.append("return", "polyline,summary");
        url.searchParams.append("apikey", effectiveApiKey);

        try {
          const res = await fetch(url);
          const data = await res.json();
          const sections = data.routes?.[0]?.sections || [];
          const lineString = new H.geo.LineString();

          // Convertit chaque polyline flexible en points et les insère
          sections.forEach((section) => {
            const poly = H.geo.LineString.fromFlexiblePolyline(section.polyline);
            for (let i = 0; i < poly.getPointCount(); i++) {
              lineString.pushPoint(poly.extractPoint(i));
            }
          });

          // 🖊️ Dessin de la route (style actuel : noir, épaisseur 2)
          routeLine = new H.map.Polyline(lineString, {
            style: { strokeColor: "#000000", lineWidth: 2 },
          });
          map.addObject(routeLine);

          // Recentrage pour inclure tous les points + itinéraire
          centerMapOnPoints();

          // 📏 Récupère résumé (longueur en mètres, durée en secondes)
          const summary = data.routes?.[0]?.sections?.[0]?.summary;
          if (summary) {
            window.TrajetInfos = {
              distance: summary.length / 1000,
              duree: summary.duration / 60,
            };
            updateTrajetInfosUI();
          }
        } catch (e) {
          console.error("Erreur de calcul itinéraire HERE:", e);
        }
      }

      // -----------------------------------------------------------------------
      // 🧩 updateSingleField(id, label) – géocode + place un marqueur + recalcul
      // - Prend la valeur du champ, géocode, ajoute le marqueur, trace la route
      // -----------------------------------------------------------------------
      function updateSingleField(id, label) {
        const val = document.getElementById(id)?.value;
        if (val?.trim()) {
          geocode(val, (coords) => {
            addMarker(coords, label);
            updateRouteLine();
          });
        }
      }

      // -----------------------------------------------------------------------
      // 🔗 setupInputListeners() – écoute l’événement personnalisé "addressUpdated"
      // - Pour chaque champ (départ, arrivée, arrêt) : on recalcule au changement
      // -----------------------------------------------------------------------
      function setupInputListeners() {
        const addressFields = [
          { id: "reservation_depart", label: "Départ" },
          { id: "reservation_arrivee", label: "Arrivée" },
          { id: "reservation_stopLieu", label: "Arrêt" },
        ];
        addressFields.forEach(({ id, label }) => {
          const input = document.getElementById(id);
          if (input) {
            input.addEventListener("addressUpdated", () => {
              updateSingleField(id, label);
            });
          }
        });
      }

      // 🔧 Active les écouteurs sur les champs d’adresse
      setupInputListeners();

      // 🧹 Expose un utilitaire pour nettoyer les listeners (si besoin)
      return {
        destroy: function () {
          observer.disconnect();
          window.removeEventListener("resize", resizeMap);
        },
      };
    },
  };
})();

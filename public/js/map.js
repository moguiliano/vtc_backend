// map.js – Initialisation de la carte HERE avec tracé GPS, centrage intelligent et gestion dynamique
(function () {
  window.ZenMap = {
    initMap: function () {
      const platform = new H.service.Platform({ apikey: window.HERE_API_KEY });
      const mapContainer = document.getElementById("mapContainer");
      if (!mapContainer)
        return console.warn("🛑 Aucun container de carte trouvé");

      const defaultLayers = platform.createDefaultLayers();
      const map = new H.Map(mapContainer, defaultLayers.vector.normal.map, {
        zoom: 11,
        center: { lat: 43.2965, lng: 5.3698 },
      });

      new H.mapevents.Behavior(new H.mapevents.MapEvents(map));
      const allPoints = {}; // Stockage des coordonnées géographiques
      let routeLine;

      function getIcon(label) {
        const svgMarkup =
          {
            Départ:
              '<svg width="24" height="24" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="10" fill="green" stroke="white" stroke-width="2"/></svg>',
            Arrivée:
              '<svg width="24" height="24" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="10" fill="red" stroke="white" stroke-width="2"/></svg>',
            Arrêt:
              '<svg width="24" height="24" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="10" fill="orange" stroke="white" stroke-width="2"/></svg>',
          }[label] ||
          '<svg width="24" height="24" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="10" fill="blue" stroke="white" stroke-width="2"/></svg>';
        return new H.map.Icon(svgMarkup);
      }

      function addMarker(coords, label) {
        const marker = new H.map.Marker(coords, { icon: getIcon(label) });
        map.addObject(marker);
        allPoints[label] = coords;
      }

      async function geocode(adresse, callback) {
        const encoded = encodeURIComponent(adresse);
        const url = `https://geocode.search.hereapi.com/v1/geocode?q=${encoded}&apikey=${window.HERE_API_KEY}`;

        try {
          const res = await fetch(url);
          const data = await res.json();
          const position = data.items[0]?.position;
          if (position) callback({ lat: position.lat, lng: position.lng });
          else console.warn("Adresse introuvable :", adresse);
        } catch (e) {
          console.error("Erreur lors du géocodage HERE :", e);
        }
      }

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
          padding:
            map.getViewPort().getHeight && map.getViewPort().getWidth
              ? {
                  top: map.getViewPort().getHeight() * 0.3,
                  left: map.getViewPort().getWidth() * 0.3,
                  bottom: map.getViewPort().getHeight() * 0.3,
                  right: map.getViewPort().getWidth() * 0.3,
                }
              : { top: 100, left: 100, bottom: 100, right: 100 },
        });
      }

      async function updateRouteLine() {
        if (routeLine) map.removeObject(routeLine);

        const waypoints = [];
        if (allPoints["Départ"]) waypoints.push(allPoints["Départ"]);
        if (allPoints["Arrêt"]) waypoints.push(allPoints["Arrêt"]);
        if (allPoints["Arrivée"]) waypoints.push(allPoints["Arrivée"]);

        if (waypoints.length < 2) return;

        const url = new URL("https://router.hereapi.com/v8/routes");
        url.searchParams.append("transportMode", "car");
        url.searchParams.append(
          "origin",
          `${waypoints[0].lat},${waypoints[0].lng}`
        );
        url.searchParams.append(
          "destination",
          `${waypoints[waypoints.length - 1].lat},${
            waypoints[waypoints.length - 1].lng
          }`
        );
        if (waypoints.length === 3) {
          url.searchParams.append(
            "via",
            `${waypoints[1].lat},${waypoints[1].lng}`
          );
        }
        url.searchParams.append("return", "polyline");
        url.searchParams.append("apikey", window.HERE_API_KEY);

        try {
          const response = await fetch(url);
          const data = await response.json();

          const sections = data.routes?.[0]?.sections || [];
          const lineString = new H.geo.LineString();

          sections.forEach((section) => {
            const poly = H.geo.LineString.fromFlexiblePolyline(
              section.polyline
            );
            for (let i = 0; i < poly.getPointCount(); i++) {
              lineString.pushPoint(poly.extractPoint(i));
            }
          });

          routeLine = new H.map.Polyline(lineString, {
            style: { strokeColor: "#2e2e2e", lineWidth: 5 },
          });
          map.addObject(routeLine);

          centerMapOnPoints();
        } catch (e) {
          console.error("Erreur de calcul d'itinéraire HERE :", e);
        }
      }

      function updateSingleField(id, label) {
        const val = document.getElementById(id)?.value;
        if (val?.trim()) {
          geocode(val, (coords) => {
            addMarker(coords, label);
            updateRouteLine();
          });
        }
      }

      function setupInputAddressUpdatedListener() {
        [
          "reservation_depart",
          "reservation_arrivee",
          "reservation_stopLieu",
        ].forEach((id) => {
          const input = document.getElementById(id);
          if (input) {
            input.addEventListener("addressUpdated", () => {
              const label = id.includes("depart")
                ? "Départ"
                : id.includes("arrivee")
                ? "Arrivée"
                : "Arrêt";
              updateSingleField(id, label);
            });
          }
        });
      }

      setupInputAddressUpdatedListener();
    },
  };
})();

// Script d'autocomplétion premium - ZenCar ✨ avec icônes, favoris, mini-carte et résultats autour de Marseille (100km)
document.addEventListener('DOMContentLoaded', () => {
    const inputs = document.querySelectorAll('[data-autocomplete="true"]');
    const cache = {};

    const defaultSuggestions = [
        { label: "Gare Saint-Charles, Marseille", isFavorite: true },
        { label: "Aéroport Marseille Provence", isFavorite: true },
        { label: "Vieux-Port de Marseille", isFavorite: true },
        { label: "Hôpital de la Timone, Marseille", isFavorite: true }
    ];

    const debounce = (func, delay) => {
        let timeout;
        return (...args) => {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), delay);
        };
    };

    const getIconForLabel = (label) => {
        label = label.toLowerCase();
        if (label.includes("aéroport")) return "✈️";
        if (label.includes("gare")) return "🚄";
        if (label.includes("port")) return "🛳️";
        if (label.includes("marseille")) return "📍";
        return "🏙️";
    };

    const getStaticMapUrl = (lat, lng) => {
        const apiKey = window.HERE_API_KEY;

        const zoom = 14;
        const w = 120, h = 80;
        const marker = `&poi=00-${lat},${lng}`;
        return `https://image.maps.ls.hereapi.com/mia/1.6/mapview?apiKey=${apiKey}&c=${lat},${lng}&z=${zoom}&w=${w}&h=${h}${marker}`;
    };

    const styleSuggestionItem = (li, index) => {
        li.style.padding = '10px 16px';
        li.style.cursor = 'pointer';
        li.style.borderTop = index === 0 ? 'none' : '1px solid #f1f1f1';
        li.style.transition = 'background 0.2s ease';
    };

    inputs.forEach(input => {
        const wrapper = document.createElement('div');
        wrapper.style.position = 'relative';
        input.parentNode.insertBefore(wrapper, input);
        wrapper.appendChild(input);

        const list = document.createElement('ul');
        list.className = 'autocomplete-list';
        Object.assign(list.style, {
            position: 'absolute', zIndex: 1000, top: '100%', left: 0, right: 0,
            background: '#fff', border: '1px solid #ddd', borderTop: 'none',
            maxHeight: '240px', overflowY: 'auto', display: 'none',
            margin: 0, padding: 0, listStyle: 'none',
            boxShadow: '0 2px 5px rgba(0,0,0,0.1)', borderRadius: '0 0 0.5rem 0.5rem'
        });
        wrapper.appendChild(list);

        let selectedIndex = -1;

        const fetchSuggestions = debounce(async () => {
            const query = input.value.trim();
            if (query.length < 2) {
                showSuggestions([], true);
                return;
            }

            if (cache[query]) {
                showSuggestions(cache[query]);
                return;
            }

            list.innerHTML = '<li style="padding:10px;text-align:center;color:#888;font-style:italic;">Chargement...</li>';
            list.style.display = 'block';

            try {
                const res = await fetch('/reservation/autocomplete?q=' + encodeURIComponent(query));
                if (!res.ok) return;
                const data = await res.json();
                cache[query] = data;
                showSuggestions(data);
            } catch (err) {
                console.error('Erreur dans l’autocomplétion :', err);
                showSuggestions([]);
            }
        }, 300);

        input.addEventListener('input', fetchSuggestions);

        input.addEventListener('keydown', (e) => {
            const items = list.querySelectorAll('li.suggestion');
            if (!items.length) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                selectedIndex = (selectedIndex + 1) % items.length;
                highlight(items, selectedIndex);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                selectedIndex = (selectedIndex - 1 + items.length) % items.length;
                highlight(items, selectedIndex);
            } else if (e.key === 'Enter') {
                if (selectedIndex >= 0) {
                    e.preventDefault();
                    input.value = items[selectedIndex].dataset.label;
                    hideSuggestions();
                }
            }
        });

        document.addEventListener('click', (e) => {
            if (!wrapper.contains(e.target)) hideSuggestions();
        });

        const showSuggestions = (suggestions, onlyFavorites = false) => {
            list.innerHTML = '';
            selectedIndex = -1;

            if (defaultSuggestions.length) {
                const favHeader = document.createElement('li');
                favHeader.textContent = '📌 Favoris';
                Object.assign(favHeader.style, {
                    fontWeight: 'bold', fontSize: '0.9rem', background: '#f9f9f9',
                    padding: '6px 12px', color: '#666'
                });
                list.appendChild(favHeader);

                defaultSuggestions.forEach((s, i) => {
                    const icon = getIconForLabel(s.label);
                    const li = document.createElement('li');
                    li.innerHTML = `<span>${icon} ${s.label}</span>`;
                    li.classList.add('suggestion');
                    li.dataset.label = s.label;
                    styleSuggestionItem(li, i);
                    li.addEventListener('mousedown', (e) => {
                        e.preventDefault();
                        input.value = s.label;
                        hideSuggestions();
                    });
                    list.appendChild(li);
                });
            }

            if (!onlyFavorites && suggestions.length) {
                const resHeader = document.createElement('li');
                resHeader.textContent = '🔎 Résultats';
                Object.assign(resHeader.style, {
                    fontWeight: 'bold', fontSize: '0.9rem', background: '#f9f9f9',
                    padding: '6px 12px', color: '#666'
                });
                list.appendChild(resHeader);

                suggestions.forEach((s, i) => {
                    const label = s.label;
                    const icon = getIconForLabel(label);
                    const li = document.createElement('li');
                    li.classList.add('suggestion');
                    li.dataset.label = label;
                    styleSuggestionItem(li, i);

                    if (s.lat && s.lng) {
                        const mapUrl = getStaticMapUrl(s.lat, s.lng);
                        li.innerHTML = `
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span>${icon} ${label}</span>
                                <img src="${mapUrl}" alt="map" style="width: 80px; height: 60px; border-radius: 4px; margin-left: 8px;">
                            </div>`;
                    } else {
                        li.innerHTML = `<span>${icon} ${label}</span>`;
                    }

                    li.addEventListener('mousedown', (e) => {
                        e.preventDefault();
                        input.value = label;
                        hideSuggestions();
                    });
                    list.appendChild(li);
                });
            }

            list.style.display = 'block';
        };

        const hideSuggestions = () => {
            list.innerHTML = '';
            list.style.display = 'none';
            selectedIndex = -1;
        };

        const highlight = (items, index) => {
            [...items].forEach((el, i) => {
                el.style.backgroundColor = i === index ? '#f0f0f0' : '';
            });
        };

        input.addEventListener('focus', () => {
            if (input.value.trim().length < 2) {
                showSuggestions([], true);
            }
        });
    });
});
/**
 * « Partager la course » (EasyAdmin, D-023 à D-028).
 *
 * Remplit les variables des modèles ({prenom}, {commission}…) avec les valeurs
 * envoyées par le serveur, recalcule commission / net en direct, et prépare
 * les liens SMS, WhatsApp, Telegram et le bouton Copier.
 *
 * Règle : une ligne dont une variable est vide disparaît (ex. pas d'arrêt).
 * Annonce groupe (D-066) : les variables de donnees.interditesGroupe sont toujours vides
 * (prix client, commission, client, adresses exactes), quel que soit le modèle.
 */
(function () {
    const racine = document.getElementById('partage-course');
    if (!racine) return;

    const donnees   = JSON.parse(racine.dataset.donnees);
    const variables = Object.assign({}, donnees.variables);
    const prix      = Number(donnees.prix) || 0;

    // ── Nombres en français ──
    const euros = (n) => n.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €';
    const lireNombre = (texte) => {
        const n = parseFloat(String(texte).replace(/\s|€|%/g, '').replace(',', '.'));
        return Number.isFinite(n) ? n : null;
    };
    const ecrireNombre = (n) => String(Math.round(n * 100) / 100).replace('.', ',');

    // ── Remplacement des variables ──
    const interditesGroupe = donnees.interditesGroupe || [];
    function valeursPour(type) {
        if (type !== 'groupe') return variables;
        const v = Object.assign({}, variables);
        interditesGroupe.forEach((nom) => { v[nom] = ''; });
        return v;
    }

    function remplir(contenu, type) {
        const variables = valeursPour(type);
        const lignes = contenu.split('\n').filter((ligne) => {
            const trouvees = ligne.match(/\{[a-z_]+\}/g) || [];
            return trouvees.every((v) => !(v in variables) || String(variables[v]).trim() !== '');
        });
        return lignes
            .join('\n')
            .replace(/\{[a-z_]+\}/g, (v) => (v in variables ? variables[v] : v))
            .replace(/\n{3,}/g, '\n\n')
            .trim();
    }

    // ── Zone de texte : s'agrandit toute seule pour montrer tout le message (D-031) ──
    function ajusterHauteur(zone) {
        if (!zone.offsetParent) return; // onglet caché : on ajuste quand il s'affiche
        zone.style.height = 'auto';
        zone.style.height = (zone.scrollHeight + 4) + 'px';
    }

    // ── Liens d'envoi ──
    const telephoneClient = (donnees.telephoneClient || '').replace(/[^\d+]/g, '');
    function liens(type, texte) {
        const numero = type === 'client' ? telephoneClient : '';
        const corps  = encodeURIComponent(texte);
        return {
            // « ?&body= » : fonctionne sur iPhone comme sur Android
            sms: 'sms:' + numero + '?&body=' + corps,
            whatsapp: 'https://wa.me/' + numero.replace(/\D/g, '') + '?text=' + corps,
            // Telegram : pas de destinataire, on choisit le groupe ou le contact dans l'appli
            telegram: 'https://t.me/share/url?url=' + corps,
        };
    }

    // ── Rafraîchit un panneau (texte + liens) ──
    function rafraichir(type) {
        const panneau = racine.querySelector('[data-panneau="' + type + '"]');
        const zone    = panneau && panneau.querySelector('[data-texte]');
        if (!zone) return;
        const choix  = panneau.querySelector('input[type=radio]:checked');
        const modele = donnees.modeles[type][choix ? Number(choix.value) : 0];
        zone.value = remplir(modele.contenu, type);
        ajusterHauteur(zone);
        majLiens(type);
    }

    function majLiens(type) {
        const panneau = racine.querySelector('[data-panneau="' + type + '"]');
        const zone    = panneau.querySelector('[data-texte]');
        const l = liens(type, zone.value);
        panneau.querySelector('[data-envoi="sms"]').href = l.sms;
        panneau.querySelector('[data-envoi="whatsapp"]').href = l.whatsapp;
        panneau.querySelector('[data-envoi="telegram"]').href = l.telegram;
    }

    const types = ['groupe', 'chauffeur', 'client'];
    const toutRafraichir = () => types.forEach(rafraichir);

    // ── Commission : € ↔ % ↔ net ──
    const champEuros   = document.getElementById('commission-euros');
    const champPercent = document.getElementById('commission-pourcent');
    const net          = document.getElementById('net-chauffeur');

    function appliquerCommission(commission) {
        variables['{commission}']    = euros(commission);
        variables['{net_chauffeur}'] = euros(prix - commission);
        net.textContent = euros(prix - commission);
        const horsLimites = commission < 0 || commission > prix;
        champEuros.classList.toggle('is-invalid', horsLimites);
        champPercent.classList.toggle('is-invalid', horsLimites);
        toutRafraichir();
    }

    champEuros.value   = ecrireNombre(donnees.commission);
    champPercent.value = prix > 0 ? ecrireNombre(donnees.commission / prix * 100) : '';
    net.textContent    = euros(prix - donnees.commission);

    champEuros.addEventListener('input', () => {
        const c = lireNombre(champEuros.value);
        if (c === null) return;
        champPercent.value = prix > 0 ? ecrireNombre(c / prix * 100) : '';
        appliquerCommission(c);
    });
    champPercent.addEventListener('input', () => {
        const p = lireNombre(champPercent.value);
        if (p === null) return;
        const c = Math.round(prix * p) / 100;
        champEuros.value = ecrireNombre(c);
        appliquerCommission(c);
    });

    // ── Zones approximatives (partage partiel) ──
    racine.querySelectorAll('input[data-variable]').forEach((champ) => {
        champ.value = variables[champ.dataset.variable] || '';
        champ.addEventListener('input', () => {
            variables[champ.dataset.variable] = champ.value.trim();
            rafraichir('groupe');
        });
    });

    // ── Onglets ──
    racine.querySelectorAll('[data-onglet]').forEach((bouton) => {
        bouton.addEventListener('click', () => {
            racine.querySelectorAll('[data-onglet]').forEach((b) => b.classList.toggle('actif', b === bouton));
            racine.querySelectorAll('[data-panneau]').forEach((p) => p.classList.toggle('actif', p.dataset.panneau === bouton.dataset.onglet));
            racine.querySelectorAll('[data-texte]').forEach(ajusterHauteur);
        });
    });

    // ── Choix du modèle, retouche du texte, Copier ──
    types.forEach((type) => {
        const panneau = racine.querySelector('[data-panneau="' + type + '"]');
        if (!panneau || !panneau.querySelector('[data-texte]')) return;

        panneau.querySelectorAll('input[type=radio]').forEach((r) => r.addEventListener('change', () => rafraichir(type)));
        panneau.querySelector('[data-texte]').addEventListener('input', (e) => { ajusterHauteur(e.target); majLiens(type); });

        const copier = panneau.querySelector('[data-envoi="copier"]');
        copier.addEventListener('click', async () => {
            const zone = panneau.querySelector('[data-texte]');
            try {
                await navigator.clipboard.writeText(zone.value);
            } catch (e) {
                zone.select();
                document.execCommand('copy');
            }
            const avant = copier.innerHTML;
            copier.innerHTML = '<i class="fa fa-check"></i> Copié !';
            setTimeout(() => { copier.innerHTML = avant; }, 1800);
        });
    });

    toutRafraichir();
})();

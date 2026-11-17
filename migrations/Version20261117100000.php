<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * D-023 à D-028 : « Partager la course ».
 *  - tables message_category (D-029) et message_template (D-028), modifiables dans EasyAdmin > Messages,
 *    avec 5 catégories et 5 modèles prêts ;
 *  - champ reservation.commission (€, null = taux par défaut de services.yaml).
 */
final class Version20261117100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Partager la course : message_category + message_template (5 + 5 prêts), reservation.commission';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE message_category (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, type VARCHAR(20) NOT NULL, ordre INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message_template (id INT AUTO_INCREMENT NOT NULL, categorie_id INT NOT NULL, nom VARCHAR(100) NOT NULL, contenu LONGTEXT NOT NULL, actif TINYINT(1) NOT NULL, ordre INT NOT NULL, INDEX IDX_9E46DB92BCF5E72D (categorie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE message_template ADD CONSTRAINT FK_9E46DB92BCF5E72D FOREIGN KEY (categorie_id) REFERENCES message_category (id)');
        $this->addSql('ALTER TABLE reservation ADD commission DOUBLE PRECISION DEFAULT NULL');

        // Une catégorie par modèle prêt, même nom ; ids fixés pour relier les modèles
        foreach (self::modeles() as $i => [$nom, $type, $contenu]) {
            $id = $i + 1;
            $this->addSql(
                'INSERT INTO message_category (id, nom, type, ordre) VALUES (?, ?, ?, ?)',
                [$id, self::CATEGORIES[$i], $type, $id]
            );
            $this->addSql(
                'INSERT INTO message_template (categorie_id, nom, contenu, actif, ordre) VALUES (?, ?, ?, 1, 1)',
                [$id, $nom, $contenu]
            );
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE message_template DROP FOREIGN KEY FK_9E46DB92BCF5E72D');
        $this->addSql('DROP TABLE message_template');
        $this->addSql('DROP TABLE message_category');
        $this->addSql('ALTER TABLE reservation DROP commission');
    }

    /** Catégorie de chaque modèle prêt, dans le même ordre que modeles(). */
    private const CATEGORIES = ['Course chauffeur', 'Annonce groupe', 'Remerciement + promo', 'Confirmation', 'Rappel'];

    /** @return array<array{string,string,string}> */
    private static function modeles(): array
    {
        return [
            ['Course chauffeur', 'chauffeur', <<<TXT
🚖 Course ZenCAR n°{reference}
📅 {date_course}
📍 Départ : {depart}
⏸️ Arrêt : {arret}
🏁 Arrivée : {arrivee}
🚗 Véhicule : {vehicule}
👶 {options}
📏 {distance} · {duree}
👤 Client : {prenom} — {telephone_client}
💶 À encaisser : {prix} ({mode_reglement})
➖ Commission ZenCAR : {commission}
✅ Net chauffeur : {net_chauffeur}
📝 {infos}
TXT],
            ['Annonce groupe', 'groupe', <<<TXT
🚖 Course disponible — ZenCAR
📅 {date_course}
📍 Prise en charge : {zone}
🏁 Arrivée : {arrivee}
🚗 Véhicule : {vehicule}
📏 {distance} · {duree}
💶 À encaisser : {prix} ({mode_reglement})
➖ Commission : {commission}
✅ Net chauffeur : {net_chauffeur}
Répondez avec votre temps d'approche.
TXT],
            ['Remerciement', 'client', <<<TXT
Bonjour {prenom}, merci pour votre confiance !
Nous espérons que votre trajet du {date_course} s'est bien passé.

Bonne nouvelle : vous bénéficiez de 10 % de réduction sur votre trajet retour. Appelez-nous ou répondez à ce message pour réserver.

Votre avis compte pour nous : {lien_avis_google}

L'équipe ZenCAR — {telephone}
TXT],
            ['Confirmation', 'client', <<<TXT
Bonjour {prenom}, votre réservation ZenCAR est confirmée.
Date : {date_course}
Départ : {depart}
Arrivée : {arrivee}
Véhicule : {vehicule}
Prix : {prix} ({mode_reglement})

Une question ? Appelez-nous au {telephone}.
L'équipe ZenCAR
TXT],
            ['Rappel la veille', 'client', <<<TXT
Bonjour {prenom}, petit rappel : votre chauffeur ZenCAR vous attend demain, {date_course}, à l'adresse : {depart}.

Un changement ? Appelez-nous au {telephone}.
L'équipe ZenCAR
TXT],
        ];
    }
}

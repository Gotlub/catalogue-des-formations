<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231022201745 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE formation (id INT AUTO_INCREMENT NOT NULL, theme_id INT NOT NULL, type_prestation_id INT DEFAULT NULL, prestataire_id INT DEFAULT NULL, groupe_id INT DEFAULT NULL, titre VARCHAR(255) NOT NULL, date_previsionnel VARCHAR(255) NOT NULL, date_realisation VARCHAR(255) DEFAULT NULL, up_dated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', date_fin_inscription DATE DEFAULT NULL, ouvert_demande TINYINT(1) NOT NULL, description VARCHAR(1000) DEFAULT NULL, validation_dsi TINYINT(1) NOT NULL, validation_drh TINYINT(1) NOT NULL, realise TINYINT(1) NOT NULL, fichier_pdf VARCHAR(255) DEFAULT NULL, INDEX IDX_404021BF59027487 (theme_id), INDEX IDX_404021BFEEA87261 (type_prestation_id), INDEX IDX_404021BFBE3DB2B7 (prestataire_id), INDEX IDX_404021BF7A45358C (groupe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE groupe_formation (id INT AUTO_INCREMENT NOT NULL, label_groupe VARCHAR(255) NOT NULL, active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE inscription (id INT AUTO_INCREMENT NOT NULL, formation_id INT NOT NULL, user_id INT NOT NULL, date_pres_inscription DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', date_demande_inscription DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', up_dated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', realise_date DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\', date_validation DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', pres_inscription TINYINT(1) NOT NULL, formation_realise TINYINT(1) NOT NULL, demande_user TINYINT(1) NOT NULL, validation_inscription TINYINT(1) NOT NULL, motivation VARCHAR(1000) DEFAULT NULL, INDEX IDX_5E90F6D65200282E (formation_id), INDEX IDX_5E90F6D6A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE prestataire (id INT AUTO_INCREMENT NOT NULL, entreprise VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE theme_formation (id INT AUTO_INCREMENT NOT NULL, intitule VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_prestation (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, up_date_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', tel VARCHAR(255) DEFAULT NULL, grade_fonction VARCHAR(255) DEFAULT NULL, date_arrive_poste VARCHAR(255) DEFAULT NULL, lieu_affectation VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE formation ADD CONSTRAINT FK_404021BF59027487 FOREIGN KEY (theme_id) REFERENCES theme_formation (id)');
        $this->addSql('ALTER TABLE formation ADD CONSTRAINT FK_404021BFEEA87261 FOREIGN KEY (type_prestation_id) REFERENCES type_prestation (id)');
        $this->addSql('ALTER TABLE formation ADD CONSTRAINT FK_404021BFBE3DB2B7 FOREIGN KEY (prestataire_id) REFERENCES prestataire (id)');
        $this->addSql('ALTER TABLE formation ADD CONSTRAINT FK_404021BF7A45358C FOREIGN KEY (groupe_id) REFERENCES groupe_formation (id)');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D65200282E FOREIGN KEY (formation_id) REFERENCES formation (id)');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE formation DROP FOREIGN KEY FK_404021BF59027487');
        $this->addSql('ALTER TABLE formation DROP FOREIGN KEY FK_404021BFEEA87261');
        $this->addSql('ALTER TABLE formation DROP FOREIGN KEY FK_404021BFBE3DB2B7');
        $this->addSql('ALTER TABLE formation DROP FOREIGN KEY FK_404021BF7A45358C');
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D65200282E');
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D6A76ED395');
        $this->addSql('DROP TABLE formation');
        $this->addSql('DROP TABLE groupe_formation');
        $this->addSql('DROP TABLE inscription');
        $this->addSql('DROP TABLE prestataire');
        $this->addSql('DROP TABLE theme_formation');
        $this->addSql('DROP TABLE type_prestation');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}

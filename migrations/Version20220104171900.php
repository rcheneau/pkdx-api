<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220104171900 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE pokemon_translation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE pokemon_type_affinity_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE pokemon_type_translation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE pokemon (id INT NOT NULL, type1_id INT NOT NULL, type2_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_62DC90F3BFAFA3E1 ON pokemon (type1_id)');
        $this->addSql('CREATE INDEX IDX_62DC90F3AD1A0C0F ON pokemon (type2_id)');
        $this->addSql('CREATE TABLE pokemon_translation (id INT NOT NULL, translatable_id INT DEFAULT NULL, locale VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3768FCC42C2AC5D3 ON pokemon_translation (translatable_id)');
        $this->addSql('CREATE TABLE pokemon_type (id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE pokemon_type_affinity (id INT NOT NULL, from_type_id INT DEFAULT NULL, to_type_id INT DEFAULT NULL, modifier DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_174E30EA43126F3C ON pokemon_type_affinity (from_type_id)');
        $this->addSql('CREATE INDEX IDX_174E30EA4BD4B166 ON pokemon_type_affinity (to_type_id)');
        $this->addSql('CREATE TABLE pokemon_type_translation (id INT NOT NULL, translatable_id INT DEFAULT NULL, locale VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_89919CD62C2AC5D3 ON pokemon_type_translation (translatable_id)');
        $this->addSql('ALTER TABLE pokemon ADD CONSTRAINT FK_62DC90F3BFAFA3E1 FOREIGN KEY (type1_id) REFERENCES pokemon_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE pokemon ADD CONSTRAINT FK_62DC90F3AD1A0C0F FOREIGN KEY (type2_id) REFERENCES pokemon_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE pokemon_translation ADD CONSTRAINT FK_3768FCC42C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES pokemon (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE pokemon_type_affinity ADD CONSTRAINT FK_174E30EA43126F3C FOREIGN KEY (from_type_id) REFERENCES pokemon_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE pokemon_type_affinity ADD CONSTRAINT FK_174E30EA4BD4B166 FOREIGN KEY (to_type_id) REFERENCES pokemon_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE pokemon_type_translation ADD CONSTRAINT FK_89919CD62C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES pokemon_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE pokemon_translation DROP CONSTRAINT FK_3768FCC42C2AC5D3');
        $this->addSql('ALTER TABLE pokemon DROP CONSTRAINT FK_62DC90F3BFAFA3E1');
        $this->addSql('ALTER TABLE pokemon DROP CONSTRAINT FK_62DC90F3AD1A0C0F');
        $this->addSql('ALTER TABLE pokemon_type_affinity DROP CONSTRAINT FK_174E30EA43126F3C');
        $this->addSql('ALTER TABLE pokemon_type_affinity DROP CONSTRAINT FK_174E30EA4BD4B166');
        $this->addSql('ALTER TABLE pokemon_type_translation DROP CONSTRAINT FK_89919CD62C2AC5D3');
        $this->addSql('DROP SEQUENCE pokemon_translation_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE pokemon_type_affinity_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE pokemon_type_translation_id_seq CASCADE');
        $this->addSql('DROP TABLE pokemon');
        $this->addSql('DROP TABLE pokemon_translation');
        $this->addSql('DROP TABLE pokemon_type');
        $this->addSql('DROP TABLE pokemon_type_affinity');
        $this->addSql('DROP TABLE pokemon_type_translation');
    }
}

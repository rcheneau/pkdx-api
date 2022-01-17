<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220117111448 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pokemon ADD height DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE pokemon ADD weight DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE pokemon ADD hp INT NOT NULL');
        $this->addSql('ALTER TABLE pokemon ADD attack INT NOT NULL');
        $this->addSql('ALTER TABLE pokemon ADD defense INT NOT NULL');
        $this->addSql('ALTER TABLE pokemon ADD sp_attack INT NOT NULL');
        $this->addSql('ALTER TABLE pokemon ADD sp_defense INT NOT NULL');
        $this->addSql('ALTER TABLE pokemon ADD speed INT NOT NULL');
        $this->addSql('ALTER TABLE pokemon ADD catch_rate INT NOT NULL');
        $this->addSql('ALTER TABLE pokemon ADD base_experience INT NOT NULL');
        $this->addSql('ALTER TABLE pokemon ADD base_friendship INT NOT NULL');
        $this->addSql('ALTER TABLE pokemon ADD growth_rate VARCHAR(30) NOT NULL');
        $this->addSql('ALTER TABLE pokemon ADD percentage_male DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE pokemon ADD egg_cycles INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE pokemon DROP height');
        $this->addSql('ALTER TABLE pokemon DROP weight');
        $this->addSql('ALTER TABLE pokemon DROP hp');
        $this->addSql('ALTER TABLE pokemon DROP attack');
        $this->addSql('ALTER TABLE pokemon DROP defense');
        $this->addSql('ALTER TABLE pokemon DROP sp_attack');
        $this->addSql('ALTER TABLE pokemon DROP sp_defense');
        $this->addSql('ALTER TABLE pokemon DROP speed');
        $this->addSql('ALTER TABLE pokemon DROP catch_rate');
        $this->addSql('ALTER TABLE pokemon DROP base_experience');
        $this->addSql('ALTER TABLE pokemon DROP base_friendship');
        $this->addSql('ALTER TABLE pokemon DROP growth_rate');
        $this->addSql('ALTER TABLE pokemon DROP percentage_male');
        $this->addSql('ALTER TABLE pokemon DROP egg_cycles');
    }
}

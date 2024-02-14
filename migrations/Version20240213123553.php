<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240213123553 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create tables for Character, Location, and Episode entities';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE character (
            id SERIAL PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            status VARCHAR(50) NOT NULL,
            species VARCHAR(50) NOT NULL,
            type VARCHAR(255) DEFAULT NULL,
            gender VARCHAR(50) NOT NULL,
            origin_id INT DEFAULT NULL,
            location_id INT DEFAULT NULL,
            image VARCHAR(255) NOT NULL,
            url VARCHAR(255) NOT NULL,
            created TIMESTAMP NOT NULL
        )');

        $this->addSql('CREATE TABLE location (
            id SERIAL PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            type VARCHAR(255) DEFAULT NULL,
            dimension VARCHAR(255) DEFAULT NULL,
            url VARCHAR(255) NOT NULL,
            created TIMESTAMP NOT NULL
        )');

        $this->addSql('CREATE TABLE episode (
            id SERIAL PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            air_date VARCHAR(255) NOT NULL,
            episode VARCHAR(255) NOT NULL,
            url VARCHAR(255) NOT NULL,
            created TIMESTAMP NOT NULL
        )');

        $this->addSql('CREATE TABLE character_episode (
            character_id INT NOT NULL,
            episode_id INT NOT NULL,
            PRIMARY KEY(character_id, episode_id)
        )');

        $this->addSql('ALTER TABLE character ADD CONSTRAINT FK_CHARACTER_ORIGIN FOREIGN KEY (origin_id) REFERENCES location (id)');
        $this->addSql('ALTER TABLE character ADD CONSTRAINT FK_CHARACTER_LOCATION FOREIGN KEY (location_id) REFERENCES location (id)');

        $this->addSql('ALTER TABLE character_episode ADD CONSTRAINT FK_CHARACTER_EPISODE_CHARACTER FOREIGN KEY (character_id) REFERENCES character (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE character_episode ADD CONSTRAINT FK_CHARACTER_EPISODE_EPISODE FOREIGN KEY (episode_id) REFERENCES episode (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE character_episode DROP CONSTRAINT FK_CHARACTER_EPISODE_CHARACTER');
        $this->addSql('ALTER TABLE character_episode DROP CONSTRAINT FK_CHARACTER_EPISODE_EPISODE');
        $this->addSql('ALTER TABLE character DROP CONSTRAINT FK_CHARACTER_ORIGIN');
        $this->addSql('ALTER TABLE character DROP CONSTRAINT FK_CHARACTER_LOCATION');
        $this->addSql('DROP TABLE character_episode');
        $this->addSql('DROP TABLE character');
        $this->addSql('DROP TABLE location');
        $this->addSql('DROP TABLE episode');
    }
}



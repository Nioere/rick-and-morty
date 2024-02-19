<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240219114340 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE character (
            id SERIAL PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            status VARCHAR(50) NOT NULL,
            species VARCHAR(50) NOT NULL,
            type VARCHAR(255) DEFAULT NULL,
            gender VARCHAR(50) NOT NULL,
            image VARCHAR(255) NOT NULL,
            created TIMESTAMP(6) NOT NULL,
            origin_id INT DEFAULT NULL,
            location_id INT DEFAULT NULL
        )');

        $this->addSql('CREATE TABLE location (
            id SERIAL PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            type VARCHAR(255) DEFAULT NULL,
            dimension VARCHAR(255) DEFAULT NULL,
            created TIMESTAMP(6) NOT NULL
        )');

        $this->addSql('CREATE TABLE episode (
            id SERIAL PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            air_date VARCHAR(255) NOT NULL,
            episode VARCHAR(255) NOT NULL,
            created TIMESTAMP(6) NOT NULL
        )');

        $this->addSql('CREATE TABLE character_episode (
            character_id INT NOT NULL,
            episode_id INT NOT NULL,
            PRIMARY KEY(character_id, episode_id)
        )');

        $this->addSql('CREATE INDEX IDX_9B1D98781136BE75 ON character_episode (character_id)');
        $this->addSql('CREATE INDEX IDX_9B1D9878362B62A0 ON character_episode (episode_id)');
        $this->addSql('ALTER TABLE character_episode ADD CONSTRAINT FK_9B1D98781136BE75 FOREIGN KEY (character_id) REFERENCES character (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE character_episode ADD CONSTRAINT FK_9B1D9878362B62A0 FOREIGN KEY (episode_id) REFERENCES episode (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql('ALTER TABLE character ADD CONSTRAINT FK_CHARACTER_ORIGIN FOREIGN KEY (origin_id) REFERENCES location (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE character ADD CONSTRAINT FK_CHARACTER_LOCATION FOREIGN KEY (location_id) REFERENCES location (id) ON DELETE SET NULL');

        $this->addSql('ALTER TABLE character ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE character ADD CONSTRAINT FK_937AB03456A273CC FOREIGN KEY (origin_id) REFERENCES location (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE character ADD CONSTRAINT FK_937AB03464D218E FOREIGN KEY (location_id) REFERENCES location (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE episode ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE location ALTER id DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE character_episode');

        $this->addSql('ALTER TABLE character DROP CONSTRAINT FK_937AB03456A273CC');
        $this->addSql('ALTER TABLE character DROP CONSTRAINT FK_937AB03464D218E');
        $this->addSql('CREATE SEQUENCE character_id_seq');
        $this->addSql('SELECT setval(\'character_id_seq\', (SELECT MAX(id) FROM character))');
        $this->addSql('ALTER TABLE character ALTER id SET DEFAULT nextval(\'character_id_seq\')');
        $this->addSql('ALTER TABLE character ADD CONSTRAINT fk_character_origin FOREIGN KEY (origin_id) REFERENCES location (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE character ADD CONSTRAINT fk_character_location FOREIGN KEY (location_id) REFERENCES location (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql('CREATE SEQUENCE episode_id_seq');
        $this->addSql('SELECT setval(\'episode_id_seq\', (SELECT MAX(id) FROM episode))');
        $this->addSql('ALTER TABLE episode ALTER id SET DEFAULT nextval(\'episode_id_seq\')');

        $this->addSql('CREATE SEQUENCE location_id_seq');
        $this->addSql('SELECT setval(\'location_id_seq\', (SELECT MAX(id) FROM location))');
        $this->addSql('ALTER TABLE location ALTER id SET DEFAULT nextval(\'location_id_seq\')');

        $this->addSql('ALTER TABLE character DROP CONSTRAINT FK_CHARACTER_ORIGIN');
        $this->addSql('ALTER TABLE character DROP CONSTRAINT FK_CHARACTER_LOCATION');
        $this->addSql('ALTER TABLE character DROP COLUMN origin_id');
        $this->addSql('ALTER TABLE character DROP COLUMN location_id');

        $this->addSql('DROP TABLE character');
        $this->addSql('DROP TABLE location');
        $this->addSql('DROP TABLE episode');
    }
}

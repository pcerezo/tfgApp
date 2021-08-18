<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210818201302 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE medicion_generica ADD archivo VARCHAR(255) NOT NULL, ADD grafico VARCHAR(255) NOT NULL, ADD temp_infrarroja DOUBLE PRECISION NOT NULL, ADD temp_sensor DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE medicion_individual DROP temp_infrarroja, DROP temp_sensor, DROP nombre, DROP media_magnitud');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE medicion_generica DROP archivo, DROP grafico, DROP temp_infrarroja, DROP temp_sensor');
        $this->addSql('ALTER TABLE medicion_individual ADD temp_infrarroja DOUBLE PRECISION NOT NULL, ADD temp_sensor DOUBLE PRECISION NOT NULL, ADD nombre VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD media_magnitud DOUBLE PRECISION NOT NULL');
    }
}

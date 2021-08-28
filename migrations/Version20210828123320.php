<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210828123320 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE medicion_generica CHANGE latitud latitud DOUBLE PRECISION NOT NULL, CHANGE longitud longitud DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE medicion_individual DROP FOREIGN KEY FK_773ECB4EB90817C3');
        $this->addSql('ALTER TABLE medicion_individual ADD CONSTRAINT FK_773ECB4EB90817C3 FOREIGN KEY (generica_id) REFERENCES medicion_generica (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE medicion_generica CHANGE latitud latitud NUMERIC(8, 6) DEFAULT NULL, CHANGE longitud longitud NUMERIC(8, 6) DEFAULT NULL');
        $this->addSql('ALTER TABLE medicion_individual DROP FOREIGN KEY FK_773ECB4EB90817C3');
        $this->addSql('ALTER TABLE medicion_individual ADD CONSTRAINT FK_773ECB4EB90817C3 FOREIGN KEY (generica_id) REFERENCES medicion_generica (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}

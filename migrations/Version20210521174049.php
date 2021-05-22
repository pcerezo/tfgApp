<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210521174049 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE medicion_individual (id INT AUTO_INCREMENT NOT NULL, id_medicion_id INT NOT NULL, declinacion DOUBLE PRECISION NOT NULL, azimut DOUBLE PRECISION NOT NULL, magnitud DOUBLE PRECISION NOT NULL, INDEX IDX_773ECB4EDD0E774C (id_medicion_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE medicion_individual ADD CONSTRAINT FK_773ECB4EDD0E774C FOREIGN KEY (id_medicion_id) REFERENCES medicion_generica (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE medicion_individual');
    }
}

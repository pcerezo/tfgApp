<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210704181647 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE archivo_medicion (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, lugar VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE medicion_generica (id INT AUTO_INCREMENT NOT NULL, fecha DATE NOT NULL, hora TIME NOT NULL, latitud DOUBLE PRECISION NOT NULL, longitud DOUBLE PRECISION NOT NULL, nombre VARCHAR(255) NOT NULL, localizacion VARCHAR(255) NOT NULL, altitud DOUBLE PRECISION NOT NULL, media_magnitud DOUBLE PRECISION NOT NULL, bat DOUBLE PRECISION NOT NULL, temp_infrarroja DOUBLE PRECISION NOT NULL, temp_sensor DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE medicion_individual (id INT AUTO_INCREMENT NOT NULL, generica_id INT NOT NULL, declinacion DOUBLE PRECISION NOT NULL, azimut DOUBLE PRECISION NOT NULL, magnitud DOUBLE PRECISION NOT NULL, INDEX IDX_773ECB4EB90817C3 (generica_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE upload (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, nombre_completo VARCHAR(255) NOT NULL, alias VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE medicion_individual ADD CONSTRAINT FK_773ECB4EB90817C3 FOREIGN KEY (generica_id) REFERENCES medicion_generica (id)');
        $this->addSql('ALTER TABLE user ADD nombrecompleto VARCHAR(255) NOT NULL, ADD foto_perfil VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE medicion_individual DROP FOREIGN KEY FK_773ECB4EB90817C3');
        $this->addSql('DROP TABLE archivo_medicion');
        $this->addSql('DROP TABLE medicion_generica');
        $this->addSql('DROP TABLE medicion_individual');
        $this->addSql('DROP TABLE upload');
        $this->addSql('ALTER TABLE `user` DROP nombrecompleto, DROP foto_perfil');
    }
}

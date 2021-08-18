<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210818191822 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE medicion_generica ADD fecha DATE NOT NULL, ADD hora TIME NOT NULL, ADD autoria VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE medicion_individual DROP fecha, DROP hora, DROP autoria');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE medicion_generica DROP fecha, DROP hora, DROP autoria');
        $this->addSql('ALTER TABLE medicion_individual ADD fecha DATE NOT NULL, ADD hora TIME NOT NULL, ADD autoria VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}

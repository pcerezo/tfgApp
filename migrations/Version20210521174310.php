<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210521174310 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE medicion_individual DROP FOREIGN KEY FK_773ECB4EDD0E774C');
        $this->addSql('DROP INDEX IDX_773ECB4EDD0E774C ON medicion_individual');
        $this->addSql('ALTER TABLE medicion_individual CHANGE id_medicion_id generica_id INT NOT NULL');
        $this->addSql('ALTER TABLE medicion_individual ADD CONSTRAINT FK_773ECB4EB90817C3 FOREIGN KEY (generica_id) REFERENCES medicion_generica (id)');
        $this->addSql('CREATE INDEX IDX_773ECB4EB90817C3 ON medicion_individual (generica_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE medicion_individual DROP FOREIGN KEY FK_773ECB4EB90817C3');
        $this->addSql('DROP INDEX IDX_773ECB4EB90817C3 ON medicion_individual');
        $this->addSql('ALTER TABLE medicion_individual CHANGE generica_id id_medicion_id INT NOT NULL');
        $this->addSql('ALTER TABLE medicion_individual ADD CONSTRAINT FK_773ECB4EDD0E774C FOREIGN KEY (id_medicion_id) REFERENCES medicion_generica (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_773ECB4EDD0E774C ON medicion_individual (id_medicion_id)');
    }
}

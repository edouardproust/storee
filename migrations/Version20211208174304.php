<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211208174304 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE purchase ADD country_id INT DEFAULT NULL, ADD firstname VARCHAR(255) NOT NULL, ADD lastname VARCHAR(255) NOT NULL, ADD street VARCHAR(255) NOT NULL, ADD postcode VARCHAR(255) NOT NULL, ADD city VARCHAR(255) NOT NULL, ADD email VARCHAR(255) NOT NULL, ADD phone VARCHAR(255) DEFAULT NULL, ADD password VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE purchase ADD CONSTRAINT FK_6117D13BF92F3E70 FOREIGN KEY (country_id) REFERENCES delivery_country (id)');
        $this->addSql('CREATE INDEX IDX_6117D13BF92F3E70 ON purchase (country_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13BF92F3E70');
        $this->addSql('DROP INDEX IDX_6117D13BF92F3E70 ON purchase');
        $this->addSql('ALTER TABLE purchase DROP country_id, DROP firstname, DROP lastname, DROP street, DROP postcode, DROP city, DROP email, DROP phone, DROP password');
    }
}

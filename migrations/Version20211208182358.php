<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211208182358 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13BF92F3E70');
        $this->addSql('DROP INDEX IDX_6117D13BF92F3E70 ON purchase');
        $this->addSql('ALTER TABLE purchase ADD country VARCHAR(255) NOT NULL, DROP user_data, CHANGE country_id delivery_country_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE purchase ADD CONSTRAINT FK_6117D13BE76AA954 FOREIGN KEY (delivery_country_id) REFERENCES delivery_country (id)');
        $this->addSql('CREATE INDEX IDX_6117D13BE76AA954 ON purchase (delivery_country_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13BE76AA954');
        $this->addSql('DROP INDEX IDX_6117D13BE76AA954 ON purchase');
        $this->addSql('ALTER TABLE purchase ADD user_data TEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP country, CHANGE delivery_country_id country_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE purchase ADD CONSTRAINT FK_6117D13BF92F3E70 FOREIGN KEY (country_id) REFERENCES delivery_country (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_6117D13BF92F3E70 ON purchase (country_id)');
    }
}

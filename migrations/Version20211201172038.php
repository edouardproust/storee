<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211201172038 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE delivery_method_delivery_country (delivery_method_id INT NOT NULL, delivery_country_id INT NOT NULL, INDEX IDX_6761888B5DED75F5 (delivery_method_id), INDEX IDX_6761888BE76AA954 (delivery_country_id), PRIMARY KEY(delivery_method_id, delivery_country_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE delivery_method_delivery_country ADD CONSTRAINT FK_6761888B5DED75F5 FOREIGN KEY (delivery_method_id) REFERENCES delivery_method (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE delivery_method_delivery_country ADD CONSTRAINT FK_6761888BE76AA954 FOREIGN KEY (delivery_country_id) REFERENCES delivery_country (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE delivery_method_delivery_country');
    }
}

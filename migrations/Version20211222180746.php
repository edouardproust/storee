<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211222180746 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product_upload (product_id INT NOT NULL, upload_id INT NOT NULL, INDEX IDX_75C1A7BB4584665A (product_id), INDEX IDX_75C1A7BBCCCFBA31 (upload_id), PRIMARY KEY(product_id, upload_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product_upload ADD CONSTRAINT FK_75C1A7BB4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_upload ADD CONSTRAINT FK_75C1A7BBCCCFBA31 FOREIGN KEY (upload_id) REFERENCES upload (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product DROP main_image');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE product_upload');
        $this->addSql('ALTER TABLE product ADD main_image VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211222181214 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE product_upload');
        $this->addSql('ALTER TABLE product ADD main_image_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADE4873418 FOREIGN KEY (main_image_id) REFERENCES upload (id)');
        $this->addSql('CREATE INDEX IDX_D34A04ADE4873418 ON product (main_image_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product_upload (product_id INT NOT NULL, upload_id INT NOT NULL, INDEX IDX_75C1A7BB4584665A (product_id), INDEX IDX_75C1A7BBCCCFBA31 (upload_id), PRIMARY KEY(product_id, upload_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE product_upload ADD CONSTRAINT FK_75C1A7BB4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_upload ADD CONSTRAINT FK_75C1A7BBCCCFBA31 FOREIGN KEY (upload_id) REFERENCES upload (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADE4873418');
        $this->addSql('DROP INDEX IDX_D34A04ADE4873418 ON product');
        $this->addSql('ALTER TABLE product DROP main_image_id');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211223070313 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE admin_setting ADD upload_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE admin_setting ADD CONSTRAINT FK_5B483A11CCCFBA31 FOREIGN KEY (upload_id) REFERENCES upload (id)');
        $this->addSql('CREATE INDEX IDX_5B483A11CCCFBA31 ON admin_setting (upload_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE admin_setting DROP FOREIGN KEY FK_5B483A11CCCFBA31');
        $this->addSql('DROP INDEX IDX_5B483A11CCCFBA31 ON admin_setting');
        $this->addSql('ALTER TABLE admin_setting DROP upload_id');
    }
}

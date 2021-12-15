<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211208213200 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13B12136921');
        $this->addSql('DROP INDEX IDX_6117D13B12136921 ON purchase');
        $this->addSql('ALTER TABLE purchase CHANGE delivery_id delivery_method_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE purchase ADD CONSTRAINT FK_6117D13B5DED75F5 FOREIGN KEY (delivery_method_id) REFERENCES delivery_method (id)');
        $this->addSql('CREATE INDEX IDX_6117D13B5DED75F5 ON purchase (delivery_method_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13B5DED75F5');
        $this->addSql('DROP INDEX IDX_6117D13B5DED75F5 ON purchase');
        $this->addSql('ALTER TABLE purchase CHANGE delivery_method_id delivery_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE purchase ADD CONSTRAINT FK_6117D13B12136921 FOREIGN KEY (delivery_id) REFERENCES delivery_method (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_6117D13B12136921 ON purchase (delivery_id)');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230930143239 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appointment ADD school_class_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F84414463F54 FOREIGN KEY (school_class_id) REFERENCES school_class (id)');
        $this->addSql('CREATE INDEX IDX_FE38F84414463F54 ON appointment (school_class_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY FK_FE38F84414463F54');
        $this->addSql('DROP INDEX IDX_FE38F84414463F54 ON appointment');
        $this->addSql('ALTER TABLE appointment DROP school_class_id');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230930154945 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE teacher_school_class (teacher_id INT NOT NULL, school_class_id INT NOT NULL, INDEX IDX_A56E5BB741807E1D (teacher_id), INDEX IDX_A56E5BB714463F54 (school_class_id), PRIMARY KEY(teacher_id, school_class_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE teacher_school_class ADD CONSTRAINT FK_A56E5BB741807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE teacher_school_class ADD CONSTRAINT FK_A56E5BB714463F54 FOREIGN KEY (school_class_id) REFERENCES school_class (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE teacher_school_class DROP FOREIGN KEY FK_A56E5BB741807E1D');
        $this->addSql('ALTER TABLE teacher_school_class DROP FOREIGN KEY FK_A56E5BB714463F54');
        $this->addSql('DROP TABLE teacher_school_class');
    }
}

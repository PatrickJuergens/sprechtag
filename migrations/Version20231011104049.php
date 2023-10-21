<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231011104049 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE teacher_time_frame (teacher_id INT NOT NULL, time_frame_id INT NOT NULL, INDEX IDX_7A957F1141807E1D (teacher_id), INDEX IDX_7A957F119B26808B (time_frame_id), PRIMARY KEY(teacher_id, time_frame_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE teacher_time_frame ADD CONSTRAINT FK_7A957F1141807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE teacher_time_frame ADD CONSTRAINT FK_7A957F119B26808B FOREIGN KEY (time_frame_id) REFERENCES time_frame (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE teacher_time_frame DROP FOREIGN KEY FK_7A957F1141807E1D');
        $this->addSql('ALTER TABLE teacher_time_frame DROP FOREIGN KEY FK_7A957F119B26808B');
        $this->addSql('DROP TABLE teacher_time_frame');
    }
}

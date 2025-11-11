<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251106084538 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE usuario CHANGE nombre nombre VARCHAR(180) NOT NULL, CHANGE contraseña contrasena VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2265B05D3A909126 ON usuario (nombre)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_2265B05D3A909126 ON usuario');
        $this->addSql('ALTER TABLE usuario CHANGE nombre nombre VARCHAR(255) NOT NULL, CHANGE contrasena contraseña VARCHAR(255) NOT NULL');
    }
}

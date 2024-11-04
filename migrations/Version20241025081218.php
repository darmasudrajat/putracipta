<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241025081218 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE hasilpond (id INT AUTO_INCREMENT NOT NULL, nomo VARCHAR(255) NOT NULL, operator VARCHAR(255) NOT NULL, good VARCHAR(255) NOT NULL, ng VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE hasil_cetak CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE nomo nomo VARCHAR(255) NOT NULL, CHANGE operator operator VARCHAR(255) NOT NULL, CHANGE good good VARCHAR(255) NOT NULL, CHANGE ng ng VARCHAR(255) NOT NULL, CHANGE mesin mesin VARCHAR(255) NOT NULL, ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE hasilpond');
        $this->addSql('ALTER TABLE hasil_cetak MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON hasil_cetak');
        $this->addSql('ALTER TABLE hasil_cetak CHANGE id id INT NOT NULL, CHANGE nomo nomo VARCHAR(1000) NOT NULL, CHANGE operator operator VARCHAR(1000) NOT NULL, CHANGE good good VARCHAR(1000) NOT NULL, CHANGE ng ng VARCHAR(1000) NOT NULL, CHANGE mesin mesin VARCHAR(1000) NOT NULL');
    }
}

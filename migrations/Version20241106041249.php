<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241106041249 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE hasil_cetak CHANGE nomo nomo VARCHAR(255) NOT NULL, CHANGE operator operator VARCHAR(255) NOT NULL, CHANGE good good VARCHAR(255) NOT NULL, CHANGE ng ng VARCHAR(255) NOT NULL, CHANGE mesin mesin VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE hasilpond CHANGE nomo nomo VARCHAR(255) NOT NULL, CHANGE operator operator VARCHAR(255) NOT NULL, CHANGE good good VARCHAR(255) NOT NULL, CHANGE ng ng VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE production_work_order_offset_printing_header DROP col1_printing_quantity, DROP col1_ink_quantity, DROP col1_plate_quantity, DROP col2_printing_quantity, DROP col2_ink_quantity, DROP col2_plate_quantity, DROP col3_printing_quantity, DROP col3_ink_quantity, DROP col3_plate_quantity, DROP col4_printing_quantity, DROP col4_ink_quantity, DROP col4_plate_quantity');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE hasilpond CHANGE nomo nomo VARCHAR(5000) NOT NULL, CHANGE operator operator VARCHAR(5000) NOT NULL, CHANGE good good BIGINT NOT NULL, CHANGE ng ng BIGINT NOT NULL');
        $this->addSql('ALTER TABLE hasil_cetak CHANGE nomo nomo VARCHAR(1000) NOT NULL, CHANGE operator operator VARCHAR(1000) NOT NULL, CHANGE good good VARCHAR(1000) NOT NULL, CHANGE ng ng VARCHAR(1000) NOT NULL, CHANGE mesin mesin VARCHAR(1000) NOT NULL');
        $this->addSql('ALTER TABLE production_work_order_offset_printing_header ADD col1_printing_quantity INT NOT NULL, ADD col1_ink_quantity NUMERIC(10, 0) NOT NULL, ADD col1_plate_quantity NUMERIC(10, 0) NOT NULL, ADD col2_printing_quantity INT NOT NULL, ADD col2_ink_quantity NUMERIC(10, 0) NOT NULL, ADD col2_plate_quantity NUMERIC(10, 0) NOT NULL, ADD col3_printing_quantity INT NOT NULL, ADD col3_ink_quantity NUMERIC(10, 0) NOT NULL, ADD col3_plate_quantity NUMERIC(10, 0) NOT NULL, ADD col4_printing_quantity INT NOT NULL, ADD col4_ink_quantity NUMERIC(10, 0) NOT NULL, ADD col4_plate_quantity NUMERIC(10, 0) NOT NULL');
    }
}

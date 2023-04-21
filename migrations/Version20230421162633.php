<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230421162633 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_details_product DROP FOREIGN KEY FK_FE10BEE04584665A');
        $this->addSql('ALTER TABLE order_details_product DROP FOREIGN KEY FK_FE10BEE08C0FA77');
        $this->addSql('ALTER TABLE order_details DROP FOREIGN KEY FK_845CA2C1F65E9B0F');
        $this->addSql('DROP TABLE order_details_product');
        $this->addSql('DROP TABLE order_details');
        $this->addSql('ALTER TABLE `order` ADD stripe_session_id VARCHAR(255) DEFAULT NULL, ADD paypal_order_id VARCHAR(255) DEFAULT NULL, CHANGE created_at created_at DATETIME NOT NULL, CHANGE delivery delivery LONGTEXT NOT NULL, CHANGE transporter_price transporter_price DOUBLE PRECISION NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE order_details_product (order_details_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_FE10BEE04584665A (product_id), INDEX IDX_FE10BEE08C0FA77 (order_details_id), PRIMARY KEY(order_details_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE order_details (id INT AUTO_INCREMENT NOT NULL, order_product_id INT DEFAULT NULL, quantity INT NOT NULL, price NUMERIC(10, 2) NOT NULL, total_recap NUMERIC(10, 2) NOT NULL, UNIQUE INDEX UNIQ_845CA2C1F65E9B0F (order_product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE order_details_product ADD CONSTRAINT FK_FE10BEE04584665A FOREIGN KEY (product_id) REFERENCES product (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_details_product ADD CONSTRAINT FK_FE10BEE08C0FA77 FOREIGN KEY (order_details_id) REFERENCES order_details (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_details ADD CONSTRAINT FK_845CA2C1F65E9B0F FOREIGN KEY (order_product_id) REFERENCES `order` (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE `order` DROP stripe_session_id, DROP paypal_order_id, CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE transporter_price transporter_price NUMERIC(10, 2) NOT NULL, CHANGE delivery delivery VARCHAR(255) NOT NULL');
    }
}

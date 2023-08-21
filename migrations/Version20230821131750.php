<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230821131750 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE pizza_ingredient_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE pizza_ingredient (id INT NOT NULL, pizza_id_id INT NOT NULL, ingredient_id_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6FF6C03F89359C8F ON pizza_ingredient (pizza_id_id)');
        $this->addSql('CREATE INDEX IDX_6FF6C03F6676F996 ON pizza_ingredient (ingredient_id_id)');
        $this->addSql('ALTER TABLE pizza_ingredient ADD CONSTRAINT FK_6FF6C03F89359C8F FOREIGN KEY (pizza_id_id) REFERENCES pizza (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE pizza_ingredient ADD CONSTRAINT FK_6FF6C03F6676F996 FOREIGN KEY (ingredient_id_id) REFERENCES ingredient (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE pizza_ingredient_id_seq CASCADE');
        $this->addSql('ALTER TABLE pizza_ingredient DROP CONSTRAINT FK_6FF6C03F89359C8F');
        $this->addSql('ALTER TABLE pizza_ingredient DROP CONSTRAINT FK_6FF6C03F6676F996');
        $this->addSql('DROP TABLE pizza_ingredient');
    }
}

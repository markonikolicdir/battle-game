<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201002190937 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE battle_log (id INT AUTO_INCREMENT NOT NULL, game_id INT NOT NULL, attacker_id INT NOT NULL, enemy_id INT NOT NULL, attacker_units DOUBLE PRECISION NOT NULL, enemy_units DOUBLE PRECISION NOT NULL, INDEX IDX_8049DBB1E48FD905 (game_id), INDEX IDX_8049DBB165F8CAE3 (attacker_id), INDEX IDX_8049DBB1900C982F (enemy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE battle_log ADD CONSTRAINT FK_8049DBB1E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE battle_log ADD CONSTRAINT FK_8049DBB165F8CAE3 FOREIGN KEY (attacker_id) REFERENCES army (id)');
        $this->addSql('ALTER TABLE battle_log ADD CONSTRAINT FK_8049DBB1900C982F FOREIGN KEY (enemy_id) REFERENCES army (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE battle_log');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241203141125 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comment (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, quack_id INTEGER NOT NULL, duck_id INTEGER NOT NULL, content CLOB NOT NULL, created_at DATETIME NOT NULL, CONSTRAINT FK_9474526CD3950CA9 FOREIGN KEY (quack_id) REFERENCES quack (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_9474526C12CF4A47 FOREIGN KEY (duck_id) REFERENCES duck (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_9474526CD3950CA9 ON comment (quack_id)');
        $this->addSql('CREATE INDEX IDX_9474526C12CF4A47 ON comment (duck_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__quack AS SELECT id, duck_id, content, created_at, image_url FROM quack');
        $this->addSql('DROP TABLE quack');
        $this->addSql('CREATE TABLE quack (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, duck_id INTEGER NOT NULL, content CLOB NOT NULL, created_at DATETIME NOT NULL, image_url CLOB DEFAULT NULL, CONSTRAINT FK_83D44F6F12CF4A47 FOREIGN KEY (duck_id) REFERENCES duck (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO quack (id, duck_id, content, created_at, image_url) SELECT id, duck_id, content, created_at, image_url FROM __temp__quack');
        $this->addSql('DROP TABLE __temp__quack');
        $this->addSql('CREATE INDEX IDX_83D44F6F12CF4A47 ON quack (duck_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE comment');
        $this->addSql('CREATE TEMPORARY TABLE __temp__quack AS SELECT id, duck_id, content, created_at, image_url FROM quack');
        $this->addSql('DROP TABLE quack');
        $this->addSql('CREATE TABLE quack (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, duck_id INTEGER NOT NULL, parent_quack_id INTEGER DEFAULT NULL, content CLOB NOT NULL, created_at DATETIME NOT NULL, image_url CLOB DEFAULT NULL, CONSTRAINT FK_83D44F6F12CF4A47 FOREIGN KEY (duck_id) REFERENCES duck (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_83D44F6F4C58A078 FOREIGN KEY (parent_quack_id) REFERENCES quack (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO quack (id, duck_id, content, created_at, image_url) SELECT id, duck_id, content, created_at, image_url FROM __temp__quack');
        $this->addSql('DROP TABLE __temp__quack');
        $this->addSql('CREATE INDEX IDX_83D44F6F12CF4A47 ON quack (duck_id)');
        $this->addSql('CREATE INDEX IDX_83D44F6F4C58A078 ON quack (parent_quack_id)');
    }
}

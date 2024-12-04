<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241203121930 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE api_user ADD created_by_id INT DEFAULT NULL, ADD updated_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE api_user ADD CONSTRAINT FK_AC64A0BAB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE api_user ADD CONSTRAINT FK_AC64A0BA896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_AC64A0BAB03A8386 ON api_user (created_by_id)');
        $this->addSql('CREATE INDEX IDX_AC64A0BA896DBBDE ON api_user (updated_by_id)');
        $this->addSql('ALTER TABLE point_of_interest ADD created_by_id INT DEFAULT NULL, ADD updated_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE point_of_interest ADD CONSTRAINT FK_E67AD359B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE point_of_interest ADD CONSTRAINT FK_E67AD359896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_E67AD359B03A8386 ON point_of_interest (created_by_id)');
        $this->addSql('CREATE INDEX IDX_E67AD359896DBBDE ON point_of_interest (updated_by_id)');
        $this->addSql('ALTER TABLE route ADD created_by_id INT DEFAULT NULL, ADD updated_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE route ADD CONSTRAINT FK_2C42079B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE route ADD CONSTRAINT FK_2C42079896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_2C42079B03A8386 ON route (created_by_id)');
        $this->addSql('CREATE INDEX IDX_2C42079896DBBDE ON route (updated_by_id)');
        $this->addSql('ALTER TABLE tags ADD created_by_id INT DEFAULT NULL, ADD updated_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tags ADD CONSTRAINT FK_6FBC9426B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE tags ADD CONSTRAINT FK_6FBC9426896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_6FBC9426B03A8386 ON tags (created_by_id)');
        $this->addSql('CREATE INDEX IDX_6FBC9426896DBBDE ON tags (updated_by_id)');
        $this->addSql('ALTER TABLE user ADD created_by_id INT DEFAULT NULL, ADD updated_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649B03A8386 ON user (created_by_id)');
        $this->addSql('CREATE INDEX IDX_8D93D649896DBBDE ON user (updated_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649B03A8386');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649896DBBDE');
        $this->addSql('DROP INDEX IDX_8D93D649B03A8386 ON user');
        $this->addSql('DROP INDEX IDX_8D93D649896DBBDE ON user');
        $this->addSql('ALTER TABLE user DROP created_by_id, DROP updated_by_id');
        $this->addSql('ALTER TABLE point_of_interest DROP FOREIGN KEY FK_E67AD359B03A8386');
        $this->addSql('ALTER TABLE point_of_interest DROP FOREIGN KEY FK_E67AD359896DBBDE');
        $this->addSql('DROP INDEX IDX_E67AD359B03A8386 ON point_of_interest');
        $this->addSql('DROP INDEX IDX_E67AD359896DBBDE ON point_of_interest');
        $this->addSql('ALTER TABLE point_of_interest DROP created_by_id, DROP updated_by_id');
        $this->addSql('ALTER TABLE route DROP FOREIGN KEY FK_2C42079B03A8386');
        $this->addSql('ALTER TABLE route DROP FOREIGN KEY FK_2C42079896DBBDE');
        $this->addSql('DROP INDEX IDX_2C42079B03A8386 ON route');
        $this->addSql('DROP INDEX IDX_2C42079896DBBDE ON route');
        $this->addSql('ALTER TABLE route DROP created_by_id, DROP updated_by_id');
        $this->addSql('ALTER TABLE api_user DROP FOREIGN KEY FK_AC64A0BAB03A8386');
        $this->addSql('ALTER TABLE api_user DROP FOREIGN KEY FK_AC64A0BA896DBBDE');
        $this->addSql('DROP INDEX IDX_AC64A0BAB03A8386 ON api_user');
        $this->addSql('DROP INDEX IDX_AC64A0BA896DBBDE ON api_user');
        $this->addSql('ALTER TABLE api_user DROP created_by_id, DROP updated_by_id');
        $this->addSql('ALTER TABLE tags DROP FOREIGN KEY FK_6FBC9426B03A8386');
        $this->addSql('ALTER TABLE tags DROP FOREIGN KEY FK_6FBC9426896DBBDE');
        $this->addSql('DROP INDEX IDX_6FBC9426B03A8386 ON tags');
        $this->addSql('DROP INDEX IDX_6FBC9426896DBBDE ON tags');
        $this->addSql('ALTER TABLE tags DROP created_by_id, DROP updated_by_id');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241218100641 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Convert string values to values in meters (distance) and seconds (total_duration)
        $this->addSql(<<<'SQL'
UPDATE
  route
SET
  distance =
    CAST(
        -- Convert string to decimal value in three steps:
        --
        -- 1. Convert comma (assumed used as decimal separator) to dot  (.)
        -- 2. Remove all characters that are not a digit or a dot
        -- 3. Convert to decimal

        -- (3)
        CAST(
            -- (2)
            REGEXP_REPLACE(
                -- (1)
                REPLACE(distance, ',', '.'),
                '[^[:digit:].]+',
                ''
            )
            AS DECIMAL(16, 8)
        )
        -- 4. Multiple by scale to convert to meters. If value contains 'km' use 1000 as scale. Otherwise, use 1.
        * (SELECT CASE WHEN distance LIKE '%km%' THEN 1000 ELSE 1 END)
    AS INT),
  total_duration =
    CAST(
        -- Convert string to decimal value in three steps:
        --
        -- 1. Convert comma (assumed used as decimal separator) to dot  (.)
        -- 2. Remove all characters that are not a digit or a dot
        -- 3. Convert to decimal

        -- (3)
        CAST(
            -- (2)
            REGEXP_REPLACE(
                -- (1)
                REPLACE(total_duration, ',', '.'),
                '[^[:digit:].]+',
                ''
            )
            AS DECIMAL(16, 8)
        )
        -- 4. Multiple by scale to convert to seconds. If value contains 'time' use 3600 as scale. Otherwise, use 60.
        * (SELECT CASE WHEN total_duration LIKE '%time%' THEN 60 * 60 ELSE 60 END)
    AS INT)
SQL);
        $this->addSql('ALTER TABLE route CHANGE distance distance INT NOT NULL, CHANGE total_duration total_duration INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE route CHANGE distance distance VARCHAR(255) NOT NULL, CHANGE total_duration total_duration VARCHAR(255) NOT NULL');
    }
}

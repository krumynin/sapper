<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200115173808 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE field (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, horizontal VARCHAR(255) NOT NULL, vertical VARCHAR(255) NOT NULL, bomb_amount VARCHAR(255) NOT NULL, data JSON NOT NULL, finished_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_5BF54558A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(254) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE jms_cron_jobs (id INT UNSIGNED AUTO_INCREMENT NOT NULL, command VARCHAR(200) NOT NULL, lastRunAt DATETIME NOT NULL, UNIQUE INDEX UNIQ_55F5ED428ECAEAD4 (command), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE jms_jobs (id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, state VARCHAR(15) NOT NULL, queue VARCHAR(50) NOT NULL, priority SMALLINT NOT NULL, createdAt DATETIME NOT NULL, startedAt DATETIME DEFAULT NULL, checkedAt DATETIME DEFAULT NULL, workerName VARCHAR(50) DEFAULT NULL, executeAfter DATETIME DEFAULT NULL, closedAt DATETIME DEFAULT NULL, command VARCHAR(255) NOT NULL, args JSON NOT NULL COMMENT \'(DC2Type:json_array)\', output LONGTEXT DEFAULT NULL, errorOutput LONGTEXT DEFAULT NULL, exitCode SMALLINT UNSIGNED DEFAULT NULL, maxRuntime SMALLINT UNSIGNED NOT NULL, maxRetries SMALLINT UNSIGNED NOT NULL, stackTrace LONGBLOB DEFAULT NULL COMMENT \'(DC2Type:jms_job_safe_object)\', runtime SMALLINT UNSIGNED DEFAULT NULL, memoryUsage INT UNSIGNED DEFAULT NULL, memoryUsageReal INT UNSIGNED DEFAULT NULL, originalJob_id BIGINT UNSIGNED DEFAULT NULL, INDEX IDX_704ADB9349C447F1 (originalJob_id), INDEX cmd_search_index (command), INDEX sorting_index (state, priority, id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE jms_job_dependencies (source_job_id BIGINT UNSIGNED NOT NULL, dest_job_id BIGINT UNSIGNED NOT NULL, INDEX IDX_8DCFE92CBD1F6B4F (source_job_id), INDEX IDX_8DCFE92C32CF8D4C (dest_job_id), PRIMARY KEY(source_job_id, dest_job_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE jms_job_related_entities (job_id BIGINT UNSIGNED NOT NULL, related_class VARCHAR(150) NOT NULL, related_id VARCHAR(100) NOT NULL, INDEX IDX_E956F4E2BE04EA9 (job_id), PRIMARY KEY(job_id, related_class, related_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE jms_job_statistics (job_id BIGINT UNSIGNED NOT NULL, characteristic VARCHAR(30) NOT NULL, createdAt DATETIME NOT NULL, charValue DOUBLE PRECISION NOT NULL, PRIMARY KEY(job_id, characteristic, createdAt)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE field ADD CONSTRAINT FK_5BF54558A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE jms_jobs ADD CONSTRAINT FK_704ADB9349C447F1 FOREIGN KEY (originalJob_id) REFERENCES jms_jobs (id)');
        $this->addSql('ALTER TABLE jms_job_dependencies ADD CONSTRAINT FK_8DCFE92CBD1F6B4F FOREIGN KEY (source_job_id) REFERENCES jms_jobs (id)');
        $this->addSql('ALTER TABLE jms_job_dependencies ADD CONSTRAINT FK_8DCFE92C32CF8D4C FOREIGN KEY (dest_job_id) REFERENCES jms_jobs (id)');
        $this->addSql('ALTER TABLE jms_job_related_entities ADD CONSTRAINT FK_E956F4E2BE04EA9 FOREIGN KEY (job_id) REFERENCES jms_jobs (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE field DROP FOREIGN KEY FK_5BF54558A76ED395');
        $this->addSql('ALTER TABLE jms_jobs DROP FOREIGN KEY FK_704ADB9349C447F1');
        $this->addSql('ALTER TABLE jms_job_dependencies DROP FOREIGN KEY FK_8DCFE92CBD1F6B4F');
        $this->addSql('ALTER TABLE jms_job_dependencies DROP FOREIGN KEY FK_8DCFE92C32CF8D4C');
        $this->addSql('ALTER TABLE jms_job_related_entities DROP FOREIGN KEY FK_E956F4E2BE04EA9');
        $this->addSql('DROP TABLE field');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE jms_cron_jobs');
        $this->addSql('DROP TABLE jms_jobs');
        $this->addSql('DROP TABLE jms_job_dependencies');
        $this->addSql('DROP TABLE jms_job_related_entities');
        $this->addSql('DROP TABLE jms_job_statistics');
    }
}

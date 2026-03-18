-- Migration: Change all id columns from INT to VARCHAR(20) for custom ID format
-- Format: YYMMDDHHmmBBBNNNN (e.g., 26022502570180001)
-- Also adds user column to master_tbl and interview_tbl

-- master_tbl: remove AUTO_INCREMENT and change to VARCHAR
ALTER TABLE `master_tbl` MODIFY `id` VARCHAR(20) NOT NULL;
ALTER TABLE `master_tbl` ADD COLUMN `user` VARCHAR(322) DEFAULT NULL;

-- interview_tbl
ALTER TABLE `interview_tbl` MODIFY `id` VARCHAR(20) DEFAULT NULL;
ALTER TABLE `interview_tbl` ADD COLUMN `user` VARCHAR(322) DEFAULT NULL;

-- demochar_tbl
ALTER TABLE `demochar_tbl` MODIFY `id` VARCHAR(20) DEFAULT NULL;

-- economicactivity_tbl
ALTER TABLE `economicactivity_tbl` MODIFY `id` VARCHAR(20) DEFAULT NULL;

-- healthinfo_tbl
ALTER TABLE `healthinfo_tbl` MODIFY `id` VARCHAR(20) DEFAULT NULL;

-- sociocivicparticipation_tbl
ALTER TABLE `sociocivicparticipation_tbl` MODIFY `id` VARCHAR(20) DEFAULT NULL;

-- migrationinfo_tbl
ALTER TABLE `migrationinfo_tbl` MODIFY `id` VARCHAR(20) DEFAULT NULL;

-- communitytaxcert_tbl
ALTER TABLE `communitytaxcert_tbl` MODIFY `id` VARCHAR(20) DEFAULT NULL;

-- skillsdevelopment_tbl
ALTER TABLE `skillsdevelopment_tbl` MODIFY `id` VARCHAR(20) DEFAULT NULL;

-- questionforhousehold_tbl
ALTER TABLE `questionforhousehold_tbl` MODIFY `id` VARCHAR(20) DEFAULT NULL;

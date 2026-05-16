create table credit_debit_retailor_amount like credit_debit_reseller_amounts;

create table retailor_plans like reseller_plans;
create table retailor_ads like reseller_ads;

ALTER TABLE `users` ADD `freeze_status` BOOLEAN NOT NULL DEFAULT FALSE AFTER `status`;


-- new queie 17 apr 2026
ALTER TABLE `channels` ADD `sport_flag` BOOLEAN NOT NULL DEFAULT FALSE AFTER `status`;



ALTER TABLE `movies` ADD `is_sd` BOOLEAN NOT NULL DEFAULT FALSE AFTER `status`;


ALTER TABLE `channels` ADD `kids_flag` BOOLEAN NOT NULL DEFAULT FALSE AFTER `sport_flag`;


ALTER TABLE `languages` ADD `order_number` INT(11) NULL DEFAULT NULL AFTER `status`;
update languages set order_number = id;

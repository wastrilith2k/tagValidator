drop database if exists validator;
create database validator;
use validator;
create table url (
 url_id int unsigned NOT NULL AUTO_INCREMENT primary key,
 test_id int unsigned not null,
 url text not null,
 title text,
 crawlable tinyint unsigned default 1,
 crawled tinyint unsigned default 0,
 return_code int unsigned,
 results text
);

create table url_relationship (
  source_id int unsigned not null,
  destination_id int unsigned not null,
  link_text text,
  test_id int unsigned not null
);

create table test(
 test_id int unsigned NOT NULL AUTO_INCREMENT primary key,
 test_name text not null,
 initial_url text,
 max_link_depth tinyint unsigned not null default 2,
 max_links_crawled int unsigned not null default 100,
 whitelist LONGTEXT,
 blacklist LONGTEXT,
 allow_offsite tinyint unsigned default 1,
 status tinyint unsigned default 0
);

create table rules (
  rule_id int unsigned NOT NULL AUTO_INCREMENT primary key,
  test_id int unsigned not null,
  param text,
  param_values text,
  last_iteration int unsigned not null default 0
);

create table rules_iterations (
  rule_id int unsigned not null,
  iteration_id int unsigned not null,
  results tinyint unsigned default 0
);
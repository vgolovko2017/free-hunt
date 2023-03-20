<?php

require 'bootstrap.php';

$data = <<<EOS
    DROP TABLE IF EXISTS `projects_skills`;
    DROP TABLE IF EXISTS `projects`;
    DROP TABLE IF EXISTS `skills`;

    CREATE TABLE `projects` (
        `id` int NOT NULL,
        `name` varchar(256) DEFAULT NULL,
        `link` varchar(1024) DEFAULT NULL,
        `budget` varchar(32) DEFAULT NULL,
        `employer_login` varchar(128) DEFAULT NULL,
        `employer_name` varchar(128) DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `name_index` (`name`),
        KEY `link_index` (`name`)
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
  
    CREATE TABLE `skills` (
        `id` int NOT NULL,
        `name` varchar(1024) DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `name_index` (`name`)
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
  
    CREATE TABLE `projects_skills` (
        `project_id` int NOT NULL,
        `skill_id` int NOT NULL,
        PRIMARY KEY (`project_id`, `skill_id`),
        FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
        FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
EOS;

try {
    $dbConnection->exec($data);
    die("\nSuccessful\n");
}
catch (\PDOException $e) {
    exit($e->getMessage());
}

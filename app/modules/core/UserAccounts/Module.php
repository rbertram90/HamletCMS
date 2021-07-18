<?php

namespace HamletCMS\UserAccounts;

use HamletCMS\HamletCMS;

class Module
{
    /**
     * Run database setup
     */
    public function install()
    {
        $dbc = HamletCMS::databaseConnection();

        $dbc->query("CREATE TABLE `users` (
            `id` int(8) NOT NULL,
            `name` varchar(30) NOT NULL,
            `surname` varchar(30) NOT NULL,
            `username` varchar(30) NOT NULL,
            `password` varchar(255) NOT NULL,
            `email` varchar(255) NOT NULL,
            `dob` date,
            `gender` varchar(10),
            `location` varchar(50),
            `profile_picture` varchar(255) NOT NULL DEFAULT 'profile_default.jpg',
            `description` text,
            `admin` tinyint(1) NOT NULL DEFAULT '0',
            `signup_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `last_login` timestamp,
            `security_q` varchar(300),
            `security_a` varchar(55)
          ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

        $dbc->query("ALTER TABLE `users` ADD PRIMARY KEY (`id`);");
        $dbc->query("ALTER TABLE `users` MODIFY `id` int(8) NOT NULL AUTO_INCREMENT;");
    }

    /**
     * Hook content
     */
    public function content($args)
    {
        switch ($args['key']) {
            case 'userProfile':

                // $args['content'].= "<div>{$numberOfPost}</div>";
                break;
        }
    }
}
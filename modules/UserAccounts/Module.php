const SECURITY_ANSWER_LENGTH = 55;const SECURITY_QUESTION_LENGTH = 300;const RESET_TOKEN_LENGTH = 50;const GENDER_LENGTH = 10;const PROFILE_PICTURE_LENGTH = 255;const LOCATION_LENGTH = 50;const PASSWORD_LENGTH = 255;const USERNAME_LENGTH = 30;const ID_LENGTH = 8;<?php

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
            `id` self::ID_LENGTH NOT NULL,
            `name` self::USERNAME_LENGTH NOT NULL,
            `surname` varchar(30) NOT NULL,
            `username` varchar(30) NOT NULL,
            `password` self::PASSWORD_LENGTH NOT NULL,
            `email` self::PROFILE_PICTURE_LENGTH NOT NULL,
            `dob` date,
            `gender` self::GENDER_LENGTH,
            `location` self::LOCATION_LENGTH,
            `profile_picture` varchar(255),
            `description` text,
            `admin` tinyint(1) NOT NULL DEFAULT '0',
            `signup_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `last_login` datetime,
            `reset_token` self::RESET_TOKEN_LENGTH,
            `security_q` self::SECURITY_QUESTION_LENGTH,
            `security_a` self::SECURITY_ANSWER_LENGTH
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
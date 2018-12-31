<?php

namespace rbwebdesigns\blogcms;

class UserAccounts
{
    /**
     * Run database setup
     */
    public function install()
    {
        $dbc = BlogCMS::databaseConnection();

        $dbc->query("CREATE TABLE `users` (
            `id` int(8) NOT NULL,
            `name` varchar(30) NOT NULL,
            `surname` varchar(30) NOT NULL,
            `username` varchar(30) NOT NULL,
            `password` varchar(255) NOT NULL,
            `email` varchar(255) NOT NULL,
            `dob` date NOT NULL,
            `gender` varchar(10) NOT NULL,
            `location` varchar(50) NOT NULL,
            `profile_picture` varchar(255) NOT NULL DEFAULT 'profile_default.jpg',
            `description` text NOT NULL,
            `admin` tinyint(1) NOT NULL DEFAULT '0',
            `signup_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `security_q` varchar(300) NOT NULL,
            `security_a` varchar(55) NOT NULL
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
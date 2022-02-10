<?php

namespace HamletCMS\UserAccounts;

class User
{
    public $id;
    public $name;
    public $surname;
    public $username;
    public $password;
    public $email;
    public $dob;
    public $gender;
    public $location;
    public $profile_picture;
    public $description;
    public $admin;
    public $signup_date;
    public $last_login;
    public $security_q;
    public $security_a;

    public function fullName()
    {
        return $this->name . ' ' . $this->surname;
    }

    public function avatar() {
        if (strlen($this->profile_picture) > 0) {
            return "/hamlet/avatars/thumbs/{$this->profile_picture}";
        }
        elseif ($this->gender == 'Female') {
            return "/hamlet/images/female_default_avatar.png";
        }
        else{
            return "/hamlet/images/male_default_avatar.png";
        }
    }

}
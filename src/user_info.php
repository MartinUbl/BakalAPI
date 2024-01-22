<?php

namespace Martinubl\Bakalapi;

class Baka_UserType {
    const STUDENT = 0;
    const TEACHER = 1;
};

class Baka_UserInfo {

    /** @var string User unique identifier (UserUID field) */
    public $uid;

    /** @var string User full name (FullName field) */
    public $fullName;

    /** @var int User role from Baka_UserType "enum" */
    public $role = Baka_UserType::STUDENT;

    /** @var string Class of the student; null if none (e.g., the user is a teacher) */
    public $studyClass = null;
};
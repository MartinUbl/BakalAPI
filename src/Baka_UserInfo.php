<?php

namespace Martinubl\Bakalapi;

use Martinubl\Bakalapi\Baka_UserType;

class Baka_UserInfo {

    /** @var string User unique identifier (UserUID field) */
    public $uid;

    /** @var string User full name (FullName field) */
    public $fullName;

    /** @var int User role from Baka_UserType "enum" */
    public $role = Baka_UserType::STUDENT;

    /** @var string Class identifier of the student; null if none (e.g., the user is a teacher) */
    public $classId = null;

    /** @var string Class name of the student; null if none (e.g., the user is a teacher) */
    public $className = null;

    public function stringify() {
        $arr = [];

        $arr['uid'] = $this->uid;
        $arr['fullName'] = $this->fullName;
        $arr['role'] = $this->role;
        $arr['classId'] = $this->classId;
        $arr['className'] = $this->className;

        return json_encode($arr);
    }

    public function destringify($string) {
        $arr = json_decode($string, true);

        $this->uid = $arr['uid'];
        $this->fullName = $arr['fullName'];
        $this->role = $arr['role'];
        $this->classId = $arr['classId'];
        $this->className = $arr['className'];
    }
};

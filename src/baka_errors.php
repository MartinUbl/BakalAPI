<?php

namespace Martinubl\Bakalapi;

class Baka_LoginError {
    const OK = 0;
    const INVALID = 1;
    const SERVER_ERROR = 2;
};

class Baka_SessionExpiredException extends \Exception {
};

<?php
namespace App\Util;

class AppException extends \RuntimeException {

    public function __construct($message, $code = 200, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

}

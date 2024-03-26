<?php
class CustomException extends HttpException {
 public function __construct($message = null, $code = 417,
Exception $previous = null) {
 if (empty($message)) {
 $message = 'This is a custom error message';
 }
 parent::__construct($message, $code, $previous);
 }
}
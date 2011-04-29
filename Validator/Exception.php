<?php
class Validator_Exception exends Exception
{
    protected $_errors = array();
    
    public function __construct($message, array $errors = array()) {
        parent::__construct($message, $errors);
        $this->_errors = $errors;
    }
    
    public function getErrors() {
        return $this->_errors;
    }
}

<?php
require_once '../Validator.php';

class FormValidatorTest extends PHPUnit_Framework_TestCase
{
    protected $_validator;
    
    public function setUp()
    {
        parent::setUp();
        $this->_validator = new Validator();
    }
    
    public function testFilterStringCallback()
    {
        $validator = $this->_validator;
        
        $data = array(
            'email' => '   test@emailwithwhitespace.com       ',
        );

        $validator->setData($data);
        $email = $validator->filter('trim')->validate('email');
        echo $email; exit;
        $this->assertEquals(strlen($email), 28);
    }
    
    /**
     * test the FormValidator email rule
     */
    public function testValidateEmail()
    {
        $validator = $this->_validator;
        
        $data = array(
            'email' => 'test@test.com',
        );
        
        $validator->email()->validate('email');
        
        $this->assertFalse($validator->hasErrors());
    }
    
    /**
     * test the FormValidator email rule on an array of emails
     */
    public function testValidateEmailArray()
    {
        $validator = $this->_validator;
        
        $data = array(
            'emails' => array(
                'test@test.com', 
                'test2@test.com', 
                'test3@test.com'
            )
        );
    
        $validator->setData($data);
        $validator->email('contains an invalid email address')->validate('emails');
        $this->assertFalse($validator->hasErrors());
    }
    
    /**
     * test the Validator 
     */
    public function testInvalidEmailArray()
    {
        $validator = $this->_validator;
        
        $data = array(
            'emails' => array(
                'test@test.com', 
                'test2@test.com', 
                'testtest.com'
            )
        );
    
        $validator->setData($data)
                    ->email()
                    ->validate('emails');

        $this->assertTrue($validator->hasErrors());
    }
}
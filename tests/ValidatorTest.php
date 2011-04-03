<?php
require_once '../Validator.php';

class ValidatorTest extends PHPUnit_Framework_TestCase
{
    protected $_validator;
    
    public function setUp()
    {
        parent::setUp();
        $this->_validator = new Validator();
    }

    /**
     * test filter with string 'trim' as the callbacl
     */
    public function testFilterTrimCallback()
    {
        $validator = $this->_validator;
        
        $data = array(
            'email' => '   test@emailwithwhitespace.com       ',
        );

        $validator->setData($data);
        $email = $validator->filter('trim')->validate('email');

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
        $validator->email()->validate('emails');
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
    
    /**
     * test the required rule with value present
     */
    public function testValidateRequired()
    {
        $validator = $this->_validator;
        
        $data = array(
            'name' => 'Test Name'
        );
    
        $validator->setData($data)
                    ->required()
                    ->validate('name');

        $this->assertFalse($validator->hasErrors());
    }
    
    /**
     * test the required rule without value present
     */
    public function testValidateRequiredEmptyVal()
    {
        $validator = $this->_validator;
        
        $data = array(
            'name' => ''
        );
    
        $validator->setData($data)
                    ->required()
                    ->validate('name');

        $this->assertTrue($validator->hasErrors());
    }
    
    public function testValidateRequiredWithArray()
    {
        $validator = $this->_validator;
        
        $data = array(
            'names' => array('Test Name', 'Another Name', 'And Another Name')
        );
    
        $validator->setData($data)
                    ->required()
                    ->validate('names');

        $this->assertFalse($validator->hasErrors());
    }
    
    /**
     * test the required rule without value present
     */
    public function testValidateRequiredWithArrayEmptyElement()
    {
        $validator = $this->_validator;
        
        $data = array(
            'names' => array('Test Name', '', 'And Another Name')
        );
    
        $validator->setData($data)
                    ->required()
                    ->validate('names');

        $this->assertTrue($validator->hasErrors());
    }
    
    /**
     * test the float rule
     */
    public function testValidateFloat()
    {
        $validator = $this->_validator;
        
        $data = array(
            'float' => 2.5
        );
    
        $validator->setData($data)
                    ->float()
                    ->validate('float');

        $this->assertFalse($validator->hasErrors());
    }
    
    /**
     * test the float rule with invalid value
     */
    public function testValidateFloatInvalidStringValue()
    {
        $validator = $this->_validator;
        
        $data = array(
            'float' => 'test'
        );
    
        $validator->setData($data)
                    ->float()
                    ->validate('float');

        $this->assertTrue($validator->hasErrors());
    }
    
    /**
     * test the integer rule
     */
    public function testValidateInteger()
    {
        $validator = $this->_validator;
        
        $data = array(
            'integer' => 20
        );
    
        $validator->setData($data)
                    ->integer()
                    ->validate('integer');

        $this->assertFalse($validator->hasErrors());
    }
    
    /**
     * test the integer rule with invalid value
     */
    public function testValidateIntegerInvalidStringValue()
    {
        $validator = $this->_validator;
        
        $data = array(
            'integer' => 'test'
        );
    
        $validator->setData($data)
                    ->integer()
                    ->validate('integer');

        $this->assertTrue($validator->hasErrors());
    }
    
    /**
     * test the digits rule
     */
    public function testValidateDigits()
    {
        $validator = $this->_validator;
        
        $data = array(
            'digits' => 20
        );
    
        $validator->setData($data)
                    ->digits()
                    ->validate('digits');

        $this->assertFalse($validator->hasErrors());
    }
    
    /**
     * test the digits rule with invalid value
     */
    public function testValidateDigitsInvalidStringValue()
    {
        $validator = $this->_validator;
        
        $data = array(
            'digits' => 'test'
        );
    
        $validator->setData($data)
                    ->digits()
                    ->validate('digits');

        $this->assertTrue($validator->hasErrors());
    }
    
    /**
     * test the min rule
     */
    public function testValidateMin()
    {
        $validator = $this->_validator;
        
        $data = array(
            'min' => 35
        );
    
        $validator->setData($data)
                    ->min(30)
                    ->validate('min');

        $this->assertFalse($validator->hasErrors());
    }
    
    /**
     * test the min rule with invalid value
     */
    public function testValidateMinInvalidValue()
    {
        $validator = $this->_validator;
        
        $data = array(
            'min' => 5
        );
    
        $validator->setData($data)
                    ->min(30)
                    ->validate('min');

        $this->assertTrue($validator->hasErrors());
    }
}
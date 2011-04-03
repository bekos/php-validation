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
        
        $data['float'] = 'test';
        
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
        
        $data['integer'] = 'test';
        
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
        
        $data['digits'] = 'test';
        
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
        
        $validator->setData($data)
                    ->min(40)
                    ->validate('min');

        $this->assertTrue($validator->hasErrors());
    }
    
    /**
     * test the max rule
     */
    public function testValidateMax()
    {
        $validator = $this->_validator;
        
        $data = array(
            'max' => 29
        );
    
        $validator->setData($data)
                    ->max(30)
                    ->validate('max');

        $this->assertFalse($validator->hasErrors());
        
        $validator->setData($data)
                    ->max(20)
                    ->validate('max');

        $this->assertTrue($validator->hasErrors());
    }
    
    /**
     * test the between rule
     */
    public function testValidateBetween()
    {
        $validator = $this->_validator;
        
        $data = array(
            'between' => 35
        );
    
        $validator->setData($data)
                    ->between(30, 40)
                    ->validate('between');

        $this->assertFalse($validator->hasErrors());

        $validator->setData($data)
                    ->between(40, 50)
                    ->validate('between');
                    
        $this->assertTrue($validator->hasErrors());
    }
    
    /**
     * test the minLength rule
     */
    public function testValidateMinLength()
    {
        $validator = $this->_validator;
        
        $data = array(
            'minlength' => 'this is a string'
        );
    
        $validator->setData($data)
                    ->minlength(10)
                    ->validate('minlength');

        $this->assertFalse($validator->hasErrors());

        $validator->setData($data)
                    ->minlength(60)
                    ->validate('minlength');
                    
        $this->assertTrue($validator->hasErrors());
    }
    
    /**
     * test the minLength rule
     */
    public function testValidateMaxLength()
    {
        $validator = $this->_validator;
        
        $data = array(
            'maxlength' => 'this is a string'
        );
    
        $validator->setData($data)
                    ->maxlength(20)
                    ->validate('maxlength');

        $this->assertFalse($validator->hasErrors());

        $validator->setData($data)
                    ->maxlength(5)
                    ->validate('maxlength');
                    
        $this->assertTrue($validator->hasErrors());
    }
    
    /**
     * test the minLength rule
     */
    public function testValidateLength()
    {
        $validator = $this->_validator;
        
        $data = array(
            'length' => 'this is a string'
        );
    
        $validator->setData($data)
                    ->length(16)
                    ->validate('length');

        $this->assertFalse($validator->hasErrors());

        $validator->setData($data)
                    ->length(5)
                    ->validate('length');
                    
        $this->assertTrue($validator->hasErrors());
    }
    
    /**
     * test the matches rule
     */
    public function testValidateMatches()
    {
        $validator = $this->_validator;
        
        $data = array(
            'password' => 'testpass',
            'password_confirm' => 'testpass'
        );
    
        $validator->setData($data)
                    ->matches('password_confirm', 'Password Confirmation')
                    ->validate('password');

        $this->assertFalse($validator->hasErrors());

        $data['password_confirm'] = 'Oh Noes I forgot what I types!';

        $validator->setData($data)
                    ->matches('password_confirmaton', 'Password Confirmation')
                    ->validate('password');
                    
        $this->assertTrue($validator->hasErrors());
    }
    
    /**
     * test the notmatches rule
     */
    public function testValidateNotMatches()
    {
        $validator = $this->_validator;

        $data = array(
            'password' => 'test',
            'password_confirm' => 'another test'
        );
    
        $validator->setData($data)
                    ->notmatches('password_confirm', 'Password Confirmation')
                    ->validate('password');

        $this->assertFalse($validator->hasErrors());

        $data['password_confirm'] = 'test';

        $validator->setData($data)
                    ->notmatches('password_confirm', 'Password Confirmation')
                    ->validate('password');

        $this->assertTrue($validator->hasErrors());
    }
    
    /**
     * test the date rule
     */
    public function testValidateDate()
    {
        $validator = $this->_validator;

        $data = array(
            'date' => '10/20/2010',
        );
    
        $validator->setData($data)
                    ->date('m/d/Y')
                    ->validate('date');
                    
        $this->assertFalse($validator->hasErrors());
        
        $data['date'] = 'test';
        
        $validator->setData($data)
                    ->date('m/d/Y')
                    ->validate('date');
                    
        $this->assertTrue($validator->hasErrors());
    }
}
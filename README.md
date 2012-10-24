# A PHP 5.3 Class for Easy Form Validation

This class follows Zend Framework naming conventions for easy drop-in as a substitute to Zend_Validation. 
If you opt out of using the bulky Zend_Form on your projects, you might choose to use this for quick and painless 
form validation.

# A Quick Example

The example below shows how to throw validation exceptions with the custom
exception. You can then retrieve the error messages from the calling method.
It is not good practice to validate your data in your controller, this should
be handled in your Model. This is just a quick example.

```php
<?php
class ExampleController extends Zend_Controller_Action {

    /**
     * Your controller action that handles validation errors, as you would
     * want these errors passed on to the view.
     *
     * @access  public
     * @return  void
     */
    public function indexAction()
    {
        try {
        
            // validate the data
            $validData = $this->_validate($_POST);
            
            // validation passed because no exception was thrown
            // ... to something with the $validData ...
            
        } catch (Validator_Exception $e) {
            // retrieve the overall error message to display
            $message = $e->getMessage();
            
            // retrieve all of the errors
            $errors = $e->getErrors();
            
            // the below code is specific to ZF
            $this->_helper->FlashMessenger(array('error' => $message));
            $this->_helper->layout->getView()->errors = $errors;
        }
    }

    /**
     * Your user-defined validation handling. The exception section is
     * very important and should always be used.
     *
     * @access  private
     * @param   array   $post
     * @return  mixed
     */
    private function _validate(array $post = array())
    {
        $validator = new Validator($post);
        $validator
            ->required('You must supply a name.')
            ->validate('name', 'Name');
        $validator
            ->required('You must supply an email address.')
            ->email('You must supply a valid email address')
            ->validate('email', 'Email');
        
        // check for errors
        if ($validator->hasErrors()) {
            throw new Validator_Exception(
                'There were errors in your form.',
                $validator->getAllErrors()
            );
        }
    
        return $validator->getValidData();
    }
    
}
```

# Available Validation Methods

* <strong>required(<em>$message = null</em>)</strong> - The field value is required.
* <strong>email(<em>$message = null</em>)</strong> - The field value must be a valid email address string.
* <strong>float(<em>$message = null</em>)</strong> - The field value must be a float.
* <strong>integer(<em>$message = null</em>)</strong> - The field value must be an integer.
* <strong>digits(<em>$message = null</em>)</strong> - The field value must be a digit (integer with no upper bounds).
* <strong>min(<em>$limit, $include = TRUE, $message = null</em>)</strong> - The field value must be greater than $limit (numeric). $include defines if the value can be equal to the limit.
* <strong>max(<em>$limit, $include = TRUE, $message = null</em>)</strong> - The field value must be less than $limit (numeric). $include defines if the value can be equal to the limit.
* <strong>between(<em>$min, $max, $include = TRUE, $message = null</em>)</strong> - The field value must be between $min and $max (numeric). $include defines if the value can be equal to $min and $max.
* <strong>minLength(<em>$length, $message = null</em>)</strong> - The field value must be greater than or equal to $length characters.
* <strong>maxLength(<em>$length, $message = null</em>)</strong> - The field value must be less than or equal to $length characters.
* <strong>length(<em>$length, $message = null</em>)</strong> - The field must be $length characters long.
* <strong>matches(<em>$field, $label, $message = null</em>)</strong> - One field matches another one (i.e. password matching)
* <strong>notMatches(<em>$field, $label, $message = null</em>)</strong> - The field value must not match the value of $field.
* <strong>startsWith(<em>$sub, $message = null</em>)</strong> - The field must start with $sub as a string.
* <strong>notStartsWith(<em>$sub, $message = null</em>)</strong> - The field must not start with $sub as a string.
* <strong>endsWith(<em>$sub, $message = null</em>)</strong> - THe field must end with $sub as a string.
* <strong>notEndsWith(<em>$sub, $message = null</em>)</strong> - The field must not end with $sub as a string.
* <strong>ip(<em>$message = null</em>)</strong> - The field value is a valid IP, determined using filter_var.
* <strong>url(<em>$message = null</em>)</strong> - The field value is a valid URL, determined using filter_var.
* <strong>date(<em>$message = null</em>)</strong> - The field value is a valid date, can be of any format accepted by DateTime()
* <strong>minDate(<em>$date, $format, $message = null</em>)</strong> - The date must be greater than $date. $format must be of a format on the page http://php.net/manual/en/datetime.createfromformat.php
* <strong>maxDate(<em>$date, $format, $message = null</em>)</strong> - The date must be less than $date. $format must be of a format on the page http://php.net/manual/en/datetime.createfromformat.php
* <strong>ccnum(<em>$message = null</em>)</strong> - The field value must be a valid credit card number.
* <strong>oneOf(<em>$allowed, $message = null</em>)</strong> - The field value must be one of the $allowed values. $allowed can be either an array or a comma-separated list of values. If comma separated, do not include spaces unless intended for matching.
* <strong>callback(<em>$callback, $message = '', $params = null</em>)</strong> - Define your own custom callback validation function. $callback must pass an is_callable() check. $params can be any value, or an array if multiple parameters must be passed.

# Validating Arrays and Array Indices

This validation class has been extended to allow for validation of arrays as well as nested indices of a multi-dimensional array.

### Validating Specific Indices

To validate specific indices of an array, use dot notation, i.e. 

```php
<?php
// load the validator
$validator = new Validator($_POST);

// ensure $_POST['field']['nested'] exists
$validator
  ->required('The nested field is required.')
  ->validate('field.nested');

// ensure we have the first two numeric indices of $_POST['links'][]
$validator
  ->required('This field is required')
  ->validate('links.0');
$validator
  ->required('This field is required')
  ->validate('links.1');
```

# Available Pre-Validation Filtering

You can apply pre-validation filters to your data (<em>i.e. trim, strip_tags, htmlentities</em>). These filters can also
be custom defined so long as they pass an <code>is_callable()</code> check.

* <strong>filter(<em>$callback</em>)</strong> 

### Filter Examples

```php
<?php
// standard php filter for valid user ids.
$validator
  ->filter('intval')
  ->min(1)
  ->validate('user_id');

// custom filter 
$validator
  ->filter(function($val) {
    // bogus formatting of the field 
    $val = rtrim($val, '/');
    $val .= '_custom_formatted';
  })
  ->validate('field_to_be_formatted');
```

# Credits


* Modifications by Corey Ballou <https://github.com/cballou> and Chris Gutierrez <https://github.com/cgutierrez>
* Forked from Tasos Bekos <tbekos at gmail dot com> which was based on the initial work of "Bretticus". 
* See http://brettic.us/2010/06/18/form-validation-class-using-php-5-3/ for the original.

# License

Copyright (c) <2012> <http://github.com/bekos, http://github.com/blackbe.lt>

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

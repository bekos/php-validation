# A PHP 5.3 Class for Easy Form Validation

## Available Pre-Validation Filtering

You can apply pre-validation filters to your data (<em>i.e. trim, strip_tags, htmlentities</em>). These filters can also
be custom defined so long as they pass an <code>is_callable()</code> check.

* <strong>filter(<em>$callback</em>)</strong> 

### Filter Examples

<code>
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
</code>

## Available Validation Methods

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

## Validating Arrays and Array Indices

This validation class has been extended to allow for validation of arrays as well as nested indices of a multi-dimensional array.

### Validating Specific Indices

To validate specific indices of an array, use dot notation, i.e. 

<code>
// load the validator
$validator = new Blackbelt_Validator($_POST);

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
</code>

## Credits

* Modifications by Corey Ballou and Chris Gutierrez.
* Forked from Tasos Bekos <tbekos at gmail dot com> which was based on the initial work of "Bretticus". 
* See http://brettic.us/2010/06/18/form-validation-class-using-php-5-3/ for the original.

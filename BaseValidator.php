<?php

/**
 * Validation library.
 *
 * This abstract class contains the skeleton of the validation library.
 * Extend to provide specific rules and error messages.
 *
 * @author Tasos Bekos <tbekos@gmail.com>
 * @see https://github.com/bekos/php-validation
 * @see Based on idea: http://brettic.us/2010/06/18/form-validation-class-using-php-5-3/
 * @abstract
 */
abstract class BaseValidator {

    /**
     * Error messages.
     *
     * @var array
     */
    protected $errors = array();

    /**
     * Vaildation rules.
     *
     * Each rule contains the ID, used to define the rule function
     * we are going to use to validate it.
     * It may contains arguments used in validation and
     * default message creation.
     * It may contains a custom message template.
     *
     * @var array with arrays
     */
    protected $rules = array();

    /**
     * Field labels used in error messages.
     *
     * @var array
     */
    protected $fields = array();

    /**
     * Function's per rule.
     *
     * @var array  of functions
     */
    protected $functions = array();

    /**
     * Data to be validated.
     *
     * If string POST|GET we use the corresponding array without the need
     * to copy the whole array in our class member.
     *
     * @var string|array
     */
    protected $data = null;

    /**
     * Default date format used in date validation rules.
     *
     * @var string
     */
    protected $defaultDateFormat = 'm/d/Y';

    /**
     * Default field label if none is provided or unknown.
     *
     * @var string
     */
    protected static $defaultFieldLabel = 'Field with the name of "%s"';

    /**
     * Global default error message,
     * if message for a specific rule is not defined in $errorMessages.
     *
     * @var string
     */
    protected static $defaultErrorMessage = '%s has an error.';

    /**
     * If a specific default error message is not present in $errorMessages,
     * then allow or not to search the parent class
     * for the corresponding message.
     *
     * Override for i18n purposes.
     *
     * @var bool
     */
    protected $searchErrorMessageInParent = FALSE;

    /**
     * Default error messages for each rule.
     *
     * @var array
     */
    private $errorMessages = array();

    /**
     * Constructor.
     * Define values to validate.
     *
     * @param string|array $data POST | GET | actual array
     */
    function __construct($data = null) {
        if (is_null($data)) {
            $data = 'POST'; // No need to duplicate POST variables
        }
        $this->data = (is_string($data)) ? strtoupper($data) : $data;

        // Error messages
        $this->errorMessages = $this->buildErrorMessages();
    }

    /**
     * Set default date format.
     *
     * @param string $format
     */
    public static function setDateFormat($format) {
        self::$defaultDateFormat = $format;
    }

    /**
     * Get proper date from value.
     *
     * @param integer|string $val
     * @return DateTime
     */
    protected function getProperDate($val, $format) {
        if (is_numeric($val)) {
            $date = new DateTime($val . ' days'); // Days difference from today
        } else {
            // Date is contained in another field
            $date = $this->getVal($val);

            if ($date === FALSE) {
                // Lastly, the actual date is passed as argument.
                $date = $val;
            }

            // Create DateTime object with the specified format.
            $date = DateTime::createFromFormat($format, $date);

            if ($date === FALSE) {
                die(__FUNCTION__ . ': Could not define proper date.');
                return $this;
            }
        }

        return $date;
    }

    /**
     * Callback to custom validation function.
     *
     * @param string $name
     * @param mixed $function
     * @param string $message
     * @return Validator
     */
    public function callback($name, $function, $message = '') {
        if (is_callable($function)) {
            // set rule and function
            $this->setRule($name, $function, $message);
        } elseif (is_string($function) && preg_match($function, 'callback') !== FALSE) {
            // we can parse this as a regexp. set rule function accordingly.
            $this->setRule($name, function($value) use ($function) {
                        return (preg_match($function, $value));
                    }, $message);
        } else {
            // just set a rule function to check equality.
            $this->setRule($name, function($value) use ( $function) {
                        return ((string) $value === (string) $function);
                    }, $message);
        }
        return $this;
    }

    /**
     * Whether errors have been found.
     *
     * @return bool
     */
    public function hasErrors() {
        return (count($this->errors) > 0);
    }

    /**
     * Get specific error.
     *
     * @param string $field
     * @return string
     */
    public function getError($field) {
        return (isset($this->errors[$field])) ? $this->errors[$field] : FALSE;
    }

    /**
     * Get all errors.
     *
     * @return array
     */
    public function getAllErrors($keys = TRUE) {
        return ($keys === TRUE) ? $this->errors : array_values($this->errors);
    }

    /**
     * Get specific value.
     *
     * @param string $key
     * @return mixed
     */
    protected function getVal($key) {
        $pos = strpos($key, '.'); // Whether we have array with dot key notation
        $index = FALSE;
        if ($pos !== FALSE) {
            $index = substr($key, $pos + 1);
            $key = substr($key, 0, $pos);
        }

        if (is_string($this->data)) {
            switch ($this->data) {
                case 'POST':
                    $value = (isset($_POST[$key])) ? $_POST[$key] : FALSE;
                    break;

                case 'GET':
                    $value = (isset($_GET[$key])) ? $_GET[$key] : FALSE;
                    break;

                default:
                    return FALSE;
                    break;
            }
        } else {
            $value = (isset($this->data[$key])) ? $this->data[$key] : FALSE;
        }

        if ($index !== FALSE && $value !== FALSE && is_array($value)) {
            // Get value in multidimensional array with dot key notation
            $value = self::_getVal(explode('.', $index), $value);
        }

        return $value;
    }

    /**
     * Navigate through a [multidimensional] array looking for a particular index.
     *
     * @param array $index The index sequence we are navigating down.
     * @param array $value The portion of the array to process.
     * @return mixed
     */
    private static function _getVal($index, $value) {
        if (is_array($index) && count($index)) {
            $currentIndex = array_shift($index);
        }

        if (isset($currentIndex) && isset($value[$currentIndex])) {
            $value = $value[$currentIndex];
        } else {
            return FALSE;
        }

        if (is_array($value) && count($value)) {
            return self::_getVal($index, $value);
        } else {
            return $value;
        }
    }

    /**
     * Set rule.
     *
     * @param string $rule Rule name.
     * @param closure $function Rule function.
     * @param string $message Custom error message if rule is violated.
     * @param array $args Arguments used in rule's validation and error message.
     */
    protected function setRule($rule, $function, $message = null, $args = array()) {
        $aRule = array('id' => $rule);

        // Custom arguments
        if (!empty($args)) {
            $aRule['args'] = $args; // Specific arguments for this rule
        }

        // Custom message
        if (!empty($message)) {
            $aRule['msg'] = $message; // User provides his own error template
        }

        // Add rule
        $this->rules[] = $aRule;

        // Cache rule's function
        if (!array_key_exists($rule, $this->functions)) {
            if (!is_callable($function)) {
                die('Invalid function for rule: ' . $rule);
            }
            $this->functions[$rule] = $function;
        }
    }

    /**
     * Get field label.
     *
     * If no label is provided from user,
     * try to find if label for specific $key is previously defined,
     * else return the default one.
     *
     * @param string $key
     * @param string $label
     * @return string
     */
    protected function getFieldLabel($key, $label) {
        if (empty($label)) {
            return (isset($this->fields[$key])) ?
                    $this->fields[$key] :
                    sprintf(static::$defaultFieldLabel, $key);
        } else {
            return $label;
        }
    }

    /**
     * Validate rules.
     *
     * @param string $key Field name.
     * @param string $label Field label.
     * @return mixed Value of field if successfully validated, FALSE otherwise.
     */
    public function validate($key, $label = '') {
        // set up field name for error message
        $this->fields[$key] = $this->getFieldLabel($key, $label);

        // Keep value for use in each rule
        $val = $this->getVal($key);

        $valid = $this->_validate($val, $key);

        $this->rules = array(); // Reset rules

        return ($valid === FALSE) ? FALSE : $val /* TRUE */;
    }

    /**
     * Validate each rule.
     *
     * @param string $val Value to be examined.
     * @param string $key
     * @return boolean TRUE if no error found, FALSE otherwise.
     */
    private function _validate($val, $key) {
        if (is_array($val)) {
            // Run validations on every element of array.
            // If one of them fails, return FALSE.
            foreach ($val as $_val) {
                if ($this->_validate($_val, $key) === FALSE) {
                    return FALSE;
                }
            }
        } else {
            // Try each rule function
            foreach ($this->rules as $rule) {
                $ruleId = $rule['id'];
                $function = $this->functions[$ruleId];

                $valid = (isset($rule['args'])) ? $function($val, $rule['args']) : $function($val);
                if ($valid === FALSE) {
                    $this->registerError($rule, $key);
                    return FALSE;
                }
            }
        }

        return TRUE;
    }

    /**
     * Register error.
     *
     * @param array $rule Rule's id, arguments and custom message.
     * @param string $key Field key.
     */
    private function registerError($rule, $key) {
        if (isset($rule['msg'])) {
            $message = $rule['msg']; // Custom message
        } else {
            $ruleId = $rule['id'];

            $args = (isset($rule['args'])) ? $rule['args'] : null;
            $message = $this->getErrorMessage($ruleId, $args);
        }

        $this->errors[$key] = sprintf($message, $this->fields[$key]);
    }

    /**
     * Get default error message for $key rule.
     *
     * @param string $key Rule id.
     * @param array $args
     * @return string Message template.
     */
    protected function getErrorMessage($key, $args = null) {
        if (isset($this->errorMessages[$key])) {
            $_message = $this->errorMessages[$key];

            if (is_callable($_message)) {
                $_message = $_message($args);
            }
        } else {
            if ($this->searchErrorMessageInParent === TRUE && ($className = get_parent_class($this)) !== FALSE) {
                $rc = new ReflectionClass($className);
                $class = $rc->newInstance();

                $_message = $class->getErrorMessage($key, $args);
            } else {

                $_message = static::$defaultErrorMessage;
            }
        }

        return $_message;
    }

    /**
     * Set default error message for rule.
     *
     * This way you can override the default error message, if you don't
     * want to extend the class you use.
     *
     * @param string $key Rule id.
     * @param string $message Message template.
     * @return bool TRUE if successfully set, FALSE otherwise.
     */
    public function setErrorMessage($key, $message) {
        if (empty($message)) {
            return FALSE;
        }

        $this->errorMessages[$key] = $message;
        return TRUE;
    }

}

?>
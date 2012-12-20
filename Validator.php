<?php

require_once 'BaseValidator.php';

/**
 * Validation rules.
 *
 * @see https://github.com/bekos/php-validation
 * @author Tasos Bekos <tbekos@gmail.com>
 */
class Validator extends BaseValidator {

    /**
     * Set default error messages.
     */
    protected function buildErrorMessages() {
        return array(
            'email' => '%s is an invalid email address.',
            'required' => '%s is required.',
            'alnum' => '%s must contain only alphanumeric characters.',
            'between' => function($args) {
                $message = '%s must be between ' . $args[0] . ' and ' . $args[1] . '.';
                if ($args[2] == FALSE) {
                    $message .= '(Without limits)';
                }
                return $message;
            },
            'integer' => '%s must consist of integer value.',
            'positive' => '%s must be a positive number.',
            'negative' => '%s must be a negative number.',
            'lowercase' => 'All characters in %s must be lowercase.',
            'uppercase' => 'All characters in %s must be uppercase.',
            'float' => '%s must be valid number.',
            'digits' => '%s must consist only of digits.',
            'min' => function($args) {
                $message = '%s must be greater than ';
                if ($args[1] == TRUE) {
                    $message .= 'or equal to ';
                }
                $message .= $args[0] . '.';

                return $message;
            },
            'max' => function($args) {
                $message = '%s must be less than ';
                if ($args[1] == TRUE) {
                    $message .= 'or equal to ';
                }
                $message .= $args[0] . '.';

                return $message;
            },
            'length' => function($args) {
                return '%s must be exactly ' . $args[0] . ' characters in length.';
            },
            'minLength' => function($args) {
                return '%s must be at least ' . $args[0] . ' characters or longer.';
            },
            'maxLength' => function($args) {
                return '%s must be no longer than ' . $args[0] . ' characters.';
            },
            'matches' => function($args) {
                return '%s must match ' . $args[1] . '.';
            },
            'notMatches' => function($args) {
                return '%s must not match ' . $args[1] . '.';
            },
            'startsWith' => function($args) {
                return '%s must start with "' . $args[0] . '".';
            },
            'notStartsWith' => function($args) {
                return '%s must not start with "' . $args[0] . '".';
            },
            'endsWith' => function($args) {
                return '%s must end with "' . $args[0] . '".';
            },
            'notEndsWith' => function($args) {
                return '%s must not end with "' . $args[0] . '".';
            },
            'ip' => '%s is an invalid IP address.',
            'url' => '%s is an invalid url.',
            'date' => function($args) {
                return '%s is not valid date (' . $args[0] . ').';
            },
            'minDate' => function($args) {
                return '%s must be later than or equal to ' . $args[0]->format($args[1]) . '.';
            },
            'maxDate' => function($args) {
                return '%s must be before ' . $args[0]->format($args[1]) . '.';
            },
            'ccnum' => '%s must be a valid credit card number.',
            'oneOf' => function($args) {
                return '%s must be one of ' . implode(', ', $args[0]) . '.';
            },
            'noneOf' => function($args) {
                return '%s must not be one of ' . implode(', ', $args[0]) . '.';
            }
        );
    }

    /**
     * Field, if completed, has to be a valid email address.
     *
     * @param string $message
     * @return Validator
     */
    public function email($message = null) {
        $this->setRule(__FUNCTION__, function($email) {
                    return (strlen($email) === 0 || filter_var($email, FILTER_VALIDATE_EMAIL) !== FALSE);
                }, $message);
        return $this;
    }

    /**
     * Field must be filled in.
     *
     * @param string $message
     * @return Validator
     */
    public function required($message = null) {
        $this->setRule(__FUNCTION__, function($string) {
                    return (strlen(trim($string)) > 0);
                }, $message);
        return $this;
    }

    /**
     * Field must contain a valid float value.
     *
     * @param string $message
     * @return Validator
     */
    public function alnum($message = null) {
        $this->setRule(__FUNCTION__, function($value) {
                    return (strlen($value) === 0 || ctype_alnum($value));
                }, $message);
        return $this;
    }

    /**
     * Field must contain not contain uppercase letters.
     *
     * @param string $message
     * @return Validator
     */
    public function lowercase($message = null) {
        $this->setRule(__FUNCTION__, function($value) {
                    return (strlen($value) === 0 || $value == mb_strtolower($value, 'UTF-8'));
                }, $message);
        return $this;
    }

    /**
     * Field must contain not contain uppercase letters.
     *
     * @param string $message
     * @return Validator
     */
    public function uppercase($message = null) {
        $this->setRule(__FUNCTION__, function($value) {
                    return (strlen($value) === 0 || $value == mb_strtoupper($value, 'UTF-8'));
                }, $message);
        return $this;
    }

    /**
     * Field must contain a valid float value.
     *
     * @param string $message
     * @return Validator
     */
    public function float($message = null) {
        $this->setRule(__FUNCTION__, function($string) {
                    return (strlen(trim($string)) === 0 || filter_var($string, FILTER_VALIDATE_FLOAT) !== FALSE);
                }, $message);
        return $this;
    }

    /**
     * Field must contain a valid integer value.
     *
     * @param string $message
     * @return Validator
     */
    public function integer($message = null) {
        $this->setRule(__FUNCTION__, function($string) {
                    return (strlen(trim($string)) === 0 || filter_var($string, FILTER_VALIDATE_INT) !== FALSE);
                }, $message);
        return $this;
    }

    /**
     * Every character in field, if completed, must be a digit.
     * This is just like integer(), except there is no upper limit.
     *
     * @param string $message
     * @return Validator
     */
    public function digits($message = null) {
        $this->setRule(__FUNCTION__, function($value) {
                    return (strlen($value) === 0 || ctype_digit((string) $value));
                }, $message);
        return $this;
    }

    /**
     * Field must be a number greater than [or equal to] X.
     *
     * @param numeric $limit
     * @param bool $include Whether to include limit value.
     * @param string $message
     * @return Validator
     */
    public function min($limit, $include = TRUE, $message = null) {
        $this->setRule(__FUNCTION__, function($value, $args) {
                    if (strlen($value) === 0) {
                        return TRUE;
                    }

                    $value = (float) $value;
                    $limit = (float) $args[0];
                    $inc = (bool) $args[1];

                    return ($value > $limit || ($inc === TRUE && $value === $limit));
                }, $message, array($limit, $include));
        return $this;
    }

    /**
     * Field must be a number greater than [or equal to] X.
     *
     * @param numeric $limit
     * @param bool $include Whether to include limit value.
     * @param string $message
     * @return Validator
     */
    public function max($limit, $include = TRUE, $message = null) {
        $this->setRule(__FUNCTION__, function($value, $args) {
                    if (strlen($value) === 0) {
                        return TRUE;
                    }

                    $value = (float) $value;
                    $limit = (float) $args[0];
                    $inc = (bool) $args[1];

                    return ($value < $limit || ($inc === TRUE && $value === $limit));
                }, $message, array($limit, $include));
        return $this;
    }

    /**
     * Field must be a number between X and Y.
     *
     * @param numeric $min
     * @param numeric $max
     * @param bool $include Whether to include limit values.
     * @param string $message
     * @return Validator
     */
    public function between($min, $max, $include = TRUE, $message = null) {
        if (empty($message)) {
            $message = $this->getErrorMessage(__FUNCTION__, array($min, $max, $include));
        }

        $this->min($min, $include, $message)->max($max, $include, $message);
        return $this;
    }

    /**
     * Field must be a positive number.
     *
     * @param string $message
     * @return Validator
     */
    public function positive($message = null) {
        if (empty($message)) {
            $message = $this->getErrorMessage(__FUNCTION__);
        }

        $this->min(0, FALSE, $message);
        return $this;
    }

    /**
     * Field must be a negative number.
     *
     * @param string $message
     * @return Validator
     */
    public function negative($message = null) {
        if (empty($message)) {
            $message = $this->getErrorMessage(__FUNCTION__);
        }

        $this->max(0, FALSE, $message);
        return $this;
    }

    /**
     * Field has to be greater than or equal to X characters long.
     *
     * @param int $len
     * @param string $message
     * @return Validator
     */
    public function minLength($len, $message = null) {
        $this->setRule(__FUNCTION__, function($string, $args) {
                    return!(strlen(trim($string)) < $args[0]);
                }, $message, array($len));
        return $this;
    }

    /**
     * Field has to be less than or equal to X characters long.
     *
     * @param int $len
     * @param string $message
     * @return Validator
     */
    public function maxLength($len, $message = null) {
        $this->setRule(__FUNCTION__, function($string, $args) {
                    return!(strlen(trim($string)) > $args[0]);
                }, $message, array($len));
        return $this;
    }

    /**
     * Field has to be X characters long.
     *
     * @param int $len
     * @param string $message
     * @return Validator
     */
    public function length($len, $message = null) {
        $this->setRule(__FUNCTION__, function($string, $args) {
                    return (strlen(trim($string)) == $args[0]);
                }, $message, array($len));
        return $this;
    }

    /**
     * Field is the same as another one (password comparison etc).
     *
     * @param string $field
     * @param string $label
     * @param string $message
     * @return Validator
     */
    public function matches($field, $label = '', $message = null) {
        $this->setRule(__FUNCTION__, function($string, $args) {
                    return ((string) $args[0] === (string) $string);
                }, $message, array($this->getVal($field), $this->getFieldLabel($field, $label)));
        return $this;
    }

    /**
     * Field is different from another one.
     *
     * @param string $field
     * @param string $label
     * @param string $message
     * @return Validator
     */
    public function notMatches($field, $label = '', $message = null) {
        $this->setRule(__FUNCTION__, function($string, $args) {
                    return ((string) $args[0] !== (string) $string);
                }, $message, array($this->getVal($field), $this->getFieldLabel($field, $label)));
        return $this;
    }

    /**
     * Field must start with a specific substring.
     *
     * @param string $sub
     * @param string $message
     * @return Validator
     */
    public function startsWith($sub, $message = null) {
        $this->setRule(__FUNCTION__, function($string, $args) {
                    $sub = $args[0];
                    return (strlen($string) === 0 || substr($string, 0, strlen($sub)) === $sub);
                }, $message, array($sub));
        return $this;
    }

    /**
     * Field must not start with a specific substring.
     *
     * @param string $sub
     * @param string $message
     * @return Validator
     */
    public function notStartsWith($sub, $message = null) {
        $this->setRule(__FUNCTION__, function($string, $args) {
                    $sub = $args[0];
                    return (strlen($string) === 0 || substr($string, 0, strlen($sub)) !== $sub);
                }, $message, array($sub));
        return $this;
    }

    /**
     * Field must end with a specific substring.
     *
     * @param string $sub
     * @param string $message
     * @return Validator
     */
    public function endsWith($sub, $message = null) {
        $this->setRule(__FUNCTION__, function($string, $args) {
                    $sub = $args[0];
                    return (strlen($string) === 0 || substr($string, -strlen($sub)) === $sub);
                }, $message, array($sub));
        return $this;
    }

    /**
     * Field must not end with a specific substring.
     *
     * @param string $sub
     * @param string $message
     * @return Validator
     */
    public function notEndsWith($sub, $message = null) {
        $this->setRule(__FUNCTION__, function($string, $args) {
                    $sub = $args[0];
                    return (strlen($string) === 0 || substr($string, -strlen($sub)) !== $sub);
                }, $message, array($sub));
        return $this;
    }

    /**
     * Field has to be valid IP address.
     *
     * @param string $message
     * @return Validator
     */
    public function ip($message = null) {
        $this->setRule(__FUNCTION__, function($string) {
                    return (strlen(trim($string)) === 0 || filter_var($string, FILTER_VALIDATE_IP));
                }, $message);
        return $this;
    }

    /**
     * Field has to be valid internet address.
     *
     * @param string $message
     * @return Validator
     */
    public function url($message = null) {
        $this->setRule(__FUNCTION__, function($string) {
                    return (strlen(trim($string)) === 0 || filter_var($string, FILTER_VALIDATE_URL));
                }, $message);
        return $this;
    }

    /**
     * Field has to be a valid date.
     *
     * @param string $message
     * @return Validator
     */
    public function date($format = null, $separator = null, $message = null) {
        if (empty($format)) {
            $format = $this->defaultDateFormat;
        }

        $this->setRule(__FUNCTION__, function($string, $args) {
                    if (strlen(trim($string)) === 0) {
                        return TRUE;
                    }

                    $separator = $args[1];
                    $dt = (is_null($separator)) ? preg_split('/[-\.\/ ]/', $string) : explode($separator, $string);

                    if ((count($dt) != 3) || !is_numeric($dt[2]) || !is_numeric($dt[1]) || !is_numeric($dt[0])) {
                        return FALSE;
                    }

                    $dateToCheck = array();
                    $format = explode('/', $args[0]);
                    foreach ($format as $i => $f) {
                        switch ($f) {
                            case 'Y':
                                $dateToCheck[2] = $dt[$i];
                                break;

                            case 'm':
                                $dateToCheck[1] = $dt[$i];
                                break;

                            case 'd':
                                $dateToCheck[0] = $dt[$i];
                                break;
                        }
                    }

                    return!(checkdate($dateToCheck[1], $dateToCheck[0], $dateToCheck[2]) === FALSE);
                }, $message, array($format, $separator));
        return $this;
    }

    /**
     * Field has to be a date later than or equal to X.
     *
     * @param string $message
     * @return Validator
     */
    public function minDate($date = 0, $format = null, $message = null) {
        if (empty($format)) {
            $format = $this->defaultDateFormat;
        }

        $date = $this->getProperDate($date, $format); // Compute DateTime

        $this->setRule(__FUNCTION__, function($string, $args) {
                    $format = $args[1];
                    $limitDate = $args[0];

                    return!($limitDate > DateTime::createFromFormat($format, $string));
                }, $message, array($date, $format));
        return $this;
    }

    /**
     * Field has to be a date later than or equal to X.
     *
     * @param string|integer $date Limit date.
     * @param string $format Date format.
     * @param string $message
     * @return Validator
     */
    public function maxDate($date = 0, $format = null, $message = null) {
        if (empty($format)) {
            $format = $this->defaultDateFormat;
        }

        $date = $this->getProperDate($date, $format); // Compute DateTime

        $this->setRule(__FUNCTION__, function($string, $args) {
                    $format = $args[1];
                    $limitDate = $args[0];

                    return!($limitDate < DateTime::createFromFormat($format, $string));
                }, $message, array($date, $format));
        return $this;
    }

    /**
     * Field has to be a valid credit card number format.
     *
     * @see https://github.com/funkatron/inspekt/blob/master/Inspekt.php
     * @param string $message
     * @return Validator
     */
    public function ccnum($message = null) {
        $this->setRule(__FUNCTION__, function($value) {
                    $value = str_replace(' ', '', $value);
                    $length = strlen($value);

                    if ($length < 13 || $length > 19) {
                        return FALSE;
                    }

                    $sum = 0;
                    $weight = 2;

                    for ($i = $length - 2; $i >= 0; $i--) {
                        $digit = $weight * $value[$i];
                        $sum += floor($digit / 10) + $digit % 10;
                        $weight = $weight % 2 + 1;
                    }

                    $mod = (10 - $sum % 10) % 10;

                    return ($mod == $value[$length - 1]);
                }, $message);
        return $this;
    }

    /**
     * Field has to be one of the allowed ones.
     *
     * @param string|array $list Allowed values.
     * @param string $message
     * @return Validator
     */
    public function oneOf($list, $message = null) {
        if (is_string($list)) {
            $list = explode(',', $list);
        }

        $this->setRule(__FUNCTION__, function($string, $args) {
                    return in_array($string, $args[0]);
                }, $message, array($list));
        return $this;
    }

    /**
     * Field must not be one of the forbidden ones.
     *
     * @param string|array $list forbidden values.
     * @param string $message
     * @return Validator
     */
    public function noneOf($list, $message = null) {
        if (is_string($list)) {
            $list = explode(',', $list);
        }

        $this->setRule(__FUNCTION__, function($string, $args) {
                    return!in_array($string, $args[0]);
                }, $message, array($list));
        return $this;
    }

}

?>

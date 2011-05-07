<?php

require_once __DIR__ . '/../Validator.php';

/**
 * Validation rules with Greek messages [and rules].
 *
 * @author Tasos Bekos <tbekos@gmail.com>
 */
class Validator_EL extends Validator {

    protected $defaultDateFormat = 'd/m/Y';
    protected static $defaultFieldLabel = 'Το πεδίο με όνομα "%s"';
    protected static $defaultErrorMessage = '%s περιέχει σφάλμα.';
    protected $searchErrorMessageInParent = FALSE;

    /**
     * Set default error messages.
     */
    protected function buildErrorMessages() {
        return array(
            'email' => '%s δεν περιέχει έγκυρο email.',
            'required' => '%s δεν έχει συμπληρωθεί.',
            'alnum' => '%s πρέπει να περιέχει μόνο αλφαριθμητικούς χαρακτήρες.',
            'between' => function($args) {
                $message = '%s πρέπει να είναι ανάμεσα στα ' . $args[0] . ' και ' . $args[1] . '.';
                if ($args[2] == FALSE) {
                    $message .= '(Χωρίς τα όρια)';
                }
                return $message;
            },
            'integer' => '%s πρέπει να είναι ακέραιος.',
            'positive' => '%s πρέπει να είναι θετικός αριθμός.',
            'negative' => '%s πρέπει να είναι αρνητικός αριθμός.',
            'lowercase' => '%s πρέπει να περιέχει μόνο πεζά γράμματα.',
            'uppercase' => '%s πρέπει να περιέχει μόνο κεφαλαία γράμματα.',
            'float' => '%s πρέπει να είναι έγκυρος αριθμός.',
            'digits' => '%s πρέπει να περιέχει αριθμούς.',
            'min' => function($args) {
                $message = '%s πρέπει να έχει τιμή μεγαλύτερη ';
                if ($args[1] == TRUE) {
                    $message .= 'ή ίση ';
                }
                $message .= 'από ' . $args[0] . '.';

                return $message;
            },
            'max' => function($args) {
                $message = '%s πρέπει να έχει τιμή μικρότερη ';
                if ($args[1] == TRUE) {
                    $message .= 'ή ίση ';
                }
                $message .= 'από ' . $args[0] . '.';

                return $message;
            },
            'length' => function($args) {
                return '%s πρέπει να περιέχει ακριβώς ' . $args[0] . ' χαρακτήρες.';
            },
            'minLength' => function($args) {
                return '%s πρέπει να περιέχει τουλάχιστον ' . $args[0] . ' χαρακτήρες.';
            },
            'maxLength' => function($args) {
                return '%s πρέπει να περιέχει το πολύ ' . $args[0] . ' χαρακτήρες.';
            },
            'matches' => function($args) {
                return '%s πρέπει να ταυτίζεται με ' . $args[1] . '.';
            },
            'notMatches' => function($args) {
                return '%s δεν πρέπει να ταυτίζεται με ' . $args[1] . '.';
            },
            'startsWith' => function($args) {
                return '%s πρέπει να ξεκινάει με "' . $args[0] . '".';
            },
            'notStartsWith' => function($args) {
                return '%s δεν πρέπει να ξεκινάει με "' . $args[0] . '".';
            },
            'endsWith' => function($args) {
                return '%s πρέπει να τελειώνει με "' . $args[0] . '".';
            },
            'notEndsWith' => function($args) {
                return '%s δεν πρέπει να τελειώνει με "' . $args[0] . '".';
            },
            'ip' => '%s δεν είναι έγκυρη IP.',
            'url' => '%s δεν είναι έγκυρο URL.',
            'date' => function($args) {
                return '%s δεν είναι έγκυρη ημερομηνία (' . $args[0] . ').';
            },
            'minDate' => function($args) {
                return '%s δεν πρέπει να προηγείται από ' . $args[0]->format($args[1]) . '.';
            },
            'maxDate' => function($args) {
                return '%s πρέπει να προηγείται από ' . $args[0]->format($args[1]) . '.';
            },
            'ccnum' => '%s δεν είναι έγκυρος αριθμός πιστωτικής κάρτας.',
            'oneOf' => function($args) {
                return '%s πρέπει να είναι ένα από: ' . implode(', ', $args[0]) . '.';
            },
            'noneOf' => function($args) {
                return '%s δεν πρέπει να είναι ένα από: ' . implode(', ', $args[0]) . '.';
            }
        );
    }

}

?>
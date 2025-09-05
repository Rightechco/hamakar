<?php
// app/core/Validator.php

class Validator {
    protected $data;
    protected $errors = [];

    public function __construct(array $data) {
        $this->data = $data;
    }

    public static function make(array $data) {
        return new static($data);
    }

    public function validate(array $rules) {
        foreach ($rules as $field => $fieldRules) {
            $value = $this->data[$field] ?? '';
            $rulesArray = explode('|', $fieldRules);

            foreach ($rulesArray as $rule) {
                if (method_exists($this, $rule)) {
                    if (!$this->$rule($field, $value)) {
                        break; // Stop validation for this field if one rule fails
                    }
                } elseif (strpos($rule, ':') !== false) {
                    list($ruleName, $param) = explode(':', $rule, 2);
                    if (method_exists($this, $ruleName)) {
                        if (!$this->$ruleName($field, $value, $param)) {
                            break;
                        }
                    }
                }
            }
        }
        return empty($this->errors);
    }

    // Validation rules
    protected function required($field, $value) {
        if (empty($value) && $value !== '0') {
            $this->errors[$field][] = "The {$field} field is required.";
            return false;
        }
        return true;
    }

    protected function email($field, $value) {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = "The {$field} field must be a valid email address.";
            return false;
        }
        return true;
    }

    protected function min($field, $value, $min) {
        if (strlen($value) < $min) {
            $this->errors[$field][] = "The {$field} must be at least {$min} characters.";
            return false;
        }
        return true;
    }

    protected function max($field, $value, $max) {
        if (strlen($value) > $max) {
            $this->errors[$field][] = "The {$field} may not be greater than {$max} characters.";
            return false;
        }
        return true;
    }

    protected function confirmed($field, $value) {
        $confirmField = $field . '_confirmation';
        if (!isset($this->data[$confirmField]) || $value !== $this->data[$confirmField]) {
            $this->errors[$field][] = "The {$field} confirmation does not match.";
            return false;
        }
        return true;
    }

    // Add more validation rules as needed (numeric, unique, etc.)

    public function errors() {
        return $this->errors;
    }
}
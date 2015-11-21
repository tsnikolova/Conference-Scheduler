<?php
namespace Framework;
use Framework\DB\SimpleDB;
class Validator
{
    private $_rules = array();
    private $_errors = array();
    public function setRule($rule, $value, $params = null, $name = null)
    {
        $this->_rules[] = array('value' => $value, 'rule' => $rule, 'params' => $params, 'name' => $name);
        return $this;
    }
    public static function matches($value1, $value2)
    {
        return $value1 == $value2;
    }
    public static function minlength($value, $lenght)
    {
        return (mb_strlen($value) >= $lenght);
    }
    public function validate()
    {
        $this->_errors = array();
        if (count($this->_rules)) {
            foreach ($this->_rules as $rule) {
                if (!$this->$rule['rule']($rule['value'], $rule['params'])) {
                    $this->_errors[] = $rule['name'];
                } else {
                    $this->_errors[] = $rule['rule'];
                }
            }
        }
        return (bool)!count($this->_errors);
    }
    public function getErrors()
    {
        if (count($this->_errors) > 0) {
            return $this->_errors;
        } else {
            return false;
        }
    }
    /**
     * Called when no function with given name exists.
     */
    public function __call($a, $b)
    {
        throw new \Exception('Invalid validation rule', 500);
    }
    public static function custom($a, $b)
    {
        if ($a instanceof \Closure) {
            return (boolean)call_user_func($a, $b);
        } else {
            throw new \Exception('Invalid custom validation rule', 500);
        }
    }
}
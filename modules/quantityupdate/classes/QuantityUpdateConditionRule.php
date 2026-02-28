<?php

class QuantityUpdateConditionRule
{
  private $value;
  private $rules;
  private $type;

  const TYPE_QTY = 1;
  const TYPE_PRICE = 2;

  public function __construct($value, $rules, $type)
  {
    $this->value = $value;
    $this->rules = $rules;
    $this->type  = $type;
  }

  public function applyRules()
  {
    if (!empty($this->rules)) {
      foreach ($this->rules as $rule) {
        $condition_is_met = $this->isConditionRuleMet($rule['condition'], $rule['value']);
        if ($condition_is_met) {
          $this->value = $this->applyConditionRuleFormula($rule['formula']);
        }
      }
    }

    return $this->value;
  }

  private function isConditionRuleMet($condition, $condition_value)
  {
    switch ($condition) {
      case 'less':
        return $this->value < $condition_value;
      case 'less_or_equal':
        return $this->value <= $condition_value;
      case 'more':
        return $this->value > $condition_value;
      case 'more_or_equal':
        return $this->value >= $condition_value;
      case 'equal':
        return $this->value == $condition_value;
      case 'zero':
        return $this->value == 0;
      case 'any':
        return true;
      default:
        return false;
    }
  }

  private function applyConditionRuleFormula($formula)
  {
    $string_math_expression = $this->value . $formula;

    if ($this->type === self::TYPE_PRICE) {
      $pattern = '/([0-9.]+)(?:\s*)([\+\-\*\/\=])(?:\s*)([0-9.]+)/';
      $precision = 6;
    } else {
      $pattern = '/([0-9.-]+)(?:\s*)([\+\-\*\=])(?:\s*)([0-9.]+)/';
      $precision = 0;
    }

    if (preg_match($pattern, $string_math_expression, $matches)) {
      $operator = $matches[2];

      switch ($operator) {
        case '+':
          $computation_result = $matches[1] + $matches[3];
          break;
        case '-':
          $computation_result = $matches[1] - $matches[3];
          break;
        case '*':
          $computation_result = $matches[1] * $matches[3];
          break;
        case '/':
          $computation_result = $matches[1] / $matches[3];
          break;
        case '=':
          $computation_result = $matches[3];
          break;
        default:
          $computation_result = $this->value;
      }
    } else {
        $computation_result = $formula;
    }

    if( is_numeric($computation_result) ){
      return Tools::ps_round($computation_result, $precision);
    }

    return $this->value;
  }
}
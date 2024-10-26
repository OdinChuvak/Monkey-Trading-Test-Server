<?php

namespace app\common;

use yii\base\DynamicModel as BaseDynamicModel;

class DynamicModel extends BaseDynamicModel
{
    public function addRules(array $rules): void
    {
        foreach ($rules as $rule) {
            $this->addRule(array_shift($rule), array_shift($rule), count($rule) > 0 ? [$rule] : []);
        }
    }
}
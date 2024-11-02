<?php

namespace app\common;

use yii\base\DynamicModel as BaseDynamicModel;

class DynamicModel extends BaseDynamicModel
{
    /**
     * @param array $rules
     * @return void
     */
    public function addRules(array $rules): void
    {
        foreach ($rules as $rule) {
            $params = [];

            foreach ($rule as $key => $value) {
                $params[] = !is_int($key) ? [$key => $value] : $value;
            }

            $this->addRule(...$params);
        }
    }
}
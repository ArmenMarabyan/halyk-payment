<?php

namespace Studioone\Halyk;

trait Validation
{
    protected function validateFields()
    {
        $missed = [];
        $calls = [];
        foreach ($this->requiredFields as $field) {
            if (!isset($this->$field) || empty($this->$field)) {
                $missed[] = $field;
                $calls[] = 'set' . ucfirst($field) . '(...)';
            }
        }

        if ($missed) {
            $message = count($missed) >= 2 ? 'Missed methods ' : 'Missed method ';
            throw new \InvalidArgumentException(
                $message . implode(', ', $calls)
            );
        }
    }
}

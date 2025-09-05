<?php

namespace App\Traits;

trait ArrayObjectManipulator
{
    public function array_group_by($key, $data)
    {
        $result = [];

        foreach ($data as $val) {
            if (property_exists($val, $key)) {
                $result[$val->$key][] = $val;
            } else {
                $result[''][] = $val;
            }
        }

        return $result;
    }
}

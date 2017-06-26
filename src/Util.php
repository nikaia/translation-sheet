<?php

namespace Nikaia\TranslationSheet;

use Illuminate\Support\Collection;

class Util
{
    public static function varExport($var, $indent = '')
    {
        switch (gettype($var)) {
            case 'string':
                return '"'.addcslashes($var, "\\\$\"\r\n\t\v\f").'"';
            case 'array':
                $indexed = array_keys($var) === range(0, count($var) - 1);
                $r = [];
                foreach ($var as $key => $value) {
                    $r[] = "$indent    "
                        .($indexed ? '' : self::varExport($key).' => ')
                        .self::varExport($value, "$indent    ");
                }

                return "[\n".implode(",\n", $r)."\n".$indent.']';
            case 'boolean':
                return $var ? 'true' : 'false';
            default:
                return var_export($var, true);
        }
    }

    public static function keyValues($values, $keys)
    {
        $values = $values instanceof Collection ? $values : new Collection($values);

        return $values->map(function ($values) use ($keys) {
            return array_combine($keys, $values);
        });
    }

    public static function asArray($value)
    {
        if (is_array($value)) {
            return $value;
        }

        return array_filter(array_map(function ($item) {
            return trim($item);
        }, explode(',', $value)));
    }
}

<?php

namespace Nikaia\TranslationSheet\Translation;

class Item
{
    public $namespace;
    public $locale;
    public $group;
    public $key;
    public $full_key;
    public $value;
    public $source_file;
    public $status;

    public static function fromArray($values)
    {
        $item = new Item;

        $item->namespace = $values['namespace'];
        $item->locale = $values['locale'];
        $item->group = $values['group'];
        $item->key = $values['key'];
        $item->full_key = $values['full_key'];
        $item->value = $values['value'];
        $item->source_file = $values['source_file'];
        $item->status = $values['status'];

        return $item;
    }
}

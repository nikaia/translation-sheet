<?php

namespace Nikaia\TranslationSheet\Translation;

use Illuminate\Support\Collection;

class Transformer
{
    private $locales = [];

    public function setLocales($locales)
    {
        $this->locales = $locales;

        return $this;
    }

    public function transform(Collection $translations)
    {
        return $translations
            ->sortBy('locale')
            ->groupBy('full_key')
            ->map(function ($translation) {
                $firstLocale = $translation->first();

                $row = [];
                $row['full_key'] = $firstLocale->full_key;

                $translation = $translation
                    ->groupBy('locale')
                    ->map(function (Collection $item) {
                        return $item->first();
                    });

                $translation = $this->ensureWeHaveAllLocales($translation);

                $localesValues = [];
                foreach ($this->locales as $locale) {
                    $item = $translation->get($locale);
                    $value = !is_null($item) && isset($item->value) ? $item->value : '';
                    $localesValues [$locale] = $value;
                }

                $row = array_merge($row, $localesValues);

                $row = array_merge($row, [
                    'namespace' => !is_null($firstLocale->namespace) ? $firstLocale->namespace : '',
                    'group' => !is_null($firstLocale->group) ? $firstLocale->group : '',
                    'key' => $firstLocale->key,
                    'source_file' => str_replace($firstLocale->locale.'/', '{locale}/', $firstLocale->source_file),
                ]);

                return array_values($row);
            })
            ->sortBy(function ($product, $key) {
                return $key;
            })
            ->values();
    }

    private function ensureWeHaveAllLocales(Collection $translation)
    {
        foreach ($this->locales as $locale) {
            if (!$translation->get($locale)) {
                $translation->put($locale, new Item);
            }
        }

        return $translation;
    }
}

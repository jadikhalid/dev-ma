<?php

namespace App\Traits;

trait HasTranslations
{
    public function getTranslated(string $field, ?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        $translations = $this->translations ?? [];

        if (isset($translations[$locale][$field])) {
            return $translations[$locale][$field];
        }

        if (isset($translations['fr'][$field])) {
            return $translations['fr'][$field];
        }

        return $this->attributes[$field] ?? null;
    }
}

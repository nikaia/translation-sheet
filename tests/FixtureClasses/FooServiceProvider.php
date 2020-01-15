<?php

namespace Nikaia\TranslationSheet\Test\FixtureClasses;

use Carbon\Laravel\ServiceProvider;

class FooServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadTranslationsFrom(
            __DIR__.'/../fixtures/basepaths/00-simple/resources/lang',
            'foo'
        );
    }
}
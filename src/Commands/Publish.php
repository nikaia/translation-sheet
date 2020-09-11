<?php


namespace Nikaia\TranslationSheet\Commands;


use Illuminate\Console\Command;

class Publish extends Command
{
    protected $signature = 'translation_sheet:publish';

    protected $description = 'Publish package config';

    public function handle()
    {
        $this->call('vendor:publish', [
            '--provider' => 'Nikaia\TranslationSheet\TranslationSheetServiceProvider',
        ]);
    }
}


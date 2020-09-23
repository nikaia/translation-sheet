<?php

namespace Nikaia\TranslationSheet\Commands;

use Illuminate\Console\Command;
use Nikaia\TranslationSheet\Sheet\TranslationsSheet;
use Nikaia\TranslationSheet\SheetPuller;
use Nikaia\TranslationSheet\Spreadsheet;

class Pull extends Command
{
    protected $signature = 'translation_sheet:pull';

    protected $description = 'Pull translations from spreadsheet and override local languages files';

    public function handle(SheetPuller $puller)
    {
        $puller
            ->setTranslationSheet(Spreadsheet::primaryTranslationSheet())
            ->withOutput($this->output)
            ->pull();
    }
}

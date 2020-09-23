<?php

namespace Nikaia\TranslationSheet\Commands;

use Illuminate\Console\Command;
use Nikaia\TranslationSheet\Sheet\TranslationsSheet;
use Nikaia\TranslationSheet\SheetPusher;
use Nikaia\TranslationSheet\Spreadsheet;

class Push extends Command
{
    protected $signature = 'translation_sheet:push';

    protected $description = 'Push translation from your local languages files to the spreadsheet';

    public function handle(SheetPusher $pusher, Spreadsheet $spreadsheet)
    {
        $spreadsheet->sheets()->each(function (TranslationsSheet $translationsSheet) use ($pusher) {
            $pusher
                ->setTranslationSheet($translationsSheet)
                ->withOutput($this->output)
                ->push();
        });
    }
}

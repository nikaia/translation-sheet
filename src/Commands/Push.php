<?php

namespace Nkreliefdev\TranslationSheet\Commands;

use Illuminate\Console\Command;
use Nkreliefdev\TranslationSheet\Sheet\TranslationsSheet;
use Nkreliefdev\TranslationSheet\SheetPusher;
use Nkreliefdev\TranslationSheet\Spreadsheet;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;

class Push extends Command
{
    protected $signature = 'translation_sheet:push';

    protected $description = 'Push translation from your local languages files to the spreadsheet';

    public function handle(SheetPusher $pusher, Spreadsheet $spreadsheet)
    {
        try {
            $spreadsheet->sheets()->each(function (TranslationsSheet $translationsSheet) use ($pusher) {
                $pusher
                    ->setTranslationsSheet($translationsSheet)
                    ->withOutput($this->output)
                    ->push();
                
                $translationsSheet->api()->reset();
            });
        } catch (DirectoryNotFoundException $e) {
            $this->error($e->getMessage());
            $this->error('Something is wrong. Did you just add an extra sheet ? try to re-run translation_sheet:setup!');
        }
    }
}

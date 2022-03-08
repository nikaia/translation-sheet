<?php

namespace Nkreliefdev\TranslationSheet\Commands;

use Illuminate\Console\Command;
use Nkreliefdev\TranslationSheet\Sheet\TranslationsSheet;
use Nkreliefdev\TranslationSheet\SheetPuller;
use Nkreliefdev\TranslationSheet\Spreadsheet;

class Pull extends Command
{
    protected $signature = 'translation_sheet:pull';

    protected $description = 'Pull translations from spreadsheet and override local languages files';

    public function handle(SheetPuller $puller, Spreadsheet $spreadsheet)
    {
        try {
            $spreadsheet->sheets()->each(function (TranslationsSheet $translationsSheet) use ($puller) {
                $puller
                    ->setTranslationsSheet($translationsSheet)
                    ->withOutput($this->output)
                    ->pull();

                $translationsSheet->api()->reset();
            });
        } catch (\ErrorException $e) {
            if ($e->getMessage() === 'Trying to access array offset on value of type null') {
                $this->error('Something is wrong. Did you just add an extra sheet ? try to re-run translation_sheet:setup!');
                return;
            }

            throw $e;
        }
    }


}

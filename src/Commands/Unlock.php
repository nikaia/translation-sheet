<?php

namespace Nikaia\TranslationSheet\Commands;

use Illuminate\Console\Command;
use Nikaia\TranslationSheet\Sheet\TranslationsSheet;
use Nikaia\TranslationSheet\Spreadsheet;

class Unlock extends Command
{
    protected $signature = 'translation_sheet:unlock';

    protected $description = 'Unlock the spreadsheet translations area.';

    public function handle(Spreadsheet $spreadsheet)
    {
        $spreadsheet->sheets()->each(function (TranslationsSheet $translationsSheet) {
            $this->info("Unlocking translation sheet [<comment>{$translationsSheet->getTitle()}</comment>] :");

            $translationsSheet->unlockTranslations();
            $translationsSheet->api()->reset();

            $this->output->writeln('<info>Done</info>.');
            $this->output->writeln(PHP_EOL);
        });
    }
}

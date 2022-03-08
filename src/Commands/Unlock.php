<?php

namespace Nkreliefdev\TranslationSheet\Commands;

use Illuminate\Console\Command;
use Nkreliefdev\TranslationSheet\Sheet\TranslationsSheet;
use Nkreliefdev\TranslationSheet\Spreadsheet;

class Unlock extends Command
{
    protected $signature = 'translation_sheet:unlock';

    protected $description = 'Unlock the spreadsheet translations area.';

    public function handle(Spreadsheet $spreadsheet)
    {
        $spreadsheet->sheets()->each(function (TranslationsSheet $translationsSheet) {
            $this->info("Locking translation sheet [<comment>{$translationsSheet->getTitle()}</comment>] :");

            $translationsSheet->unlockTranslations();
            $translationsSheet->api()->reset();

            $this->output->writeln('<info>Done</info>.');
            $this->output->writeln(PHP_EOL);
        });
    }
}

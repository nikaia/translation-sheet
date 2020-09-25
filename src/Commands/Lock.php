<?php

namespace Nikaia\TranslationSheet\Commands;

use Illuminate\Console\Command;
use Nikaia\TranslationSheet\Sheet\TranslationsSheet;
use Nikaia\TranslationSheet\Spreadsheet;

class Lock extends Command
{
    protected $signature = 'translation_sheet:lock';

    protected $description = 'Lock the spreadsheet translations area, to prevent changes.';

    public function handle(Spreadsheet $spreadsheet)
    {
        $spreadsheet->sheets()->each(function (TranslationsSheet $translationsSheet) {
            $this->info("Locking translation sheet [<comment>{$translationsSheet->getTitle()}</comment>] :");

            $translationsSheet->lockTranslations();
            $translationsSheet->api()->reset();

            $this->output->writeln('<info>Done</info>.');
            $this->output->writeln(PHP_EOL);
        });
    }
}

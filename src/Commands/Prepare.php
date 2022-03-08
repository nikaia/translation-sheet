<?php

namespace Nkreliefdev\TranslationSheet\Commands;

use Illuminate\Console\Command;
use Nkreliefdev\TranslationSheet\SheetPusher;
use Nkreliefdev\TranslationSheet\Sheet\TranslationsSheet;
use Nkreliefdev\TranslationSheet\Spreadsheet;
use Nkreliefdev\TranslationSheet\Translation\Writer;
use Nkreliefdev\TranslationSheet\Util;

class Prepare extends Command
{
    protected $signature = 'translation_sheet:prepare';

    protected $description = 'Rewrite locales languages files by removing comments and sorting keys. This most likely reduce and simplify conflicts when pulling translations from spreadsheet.';

    public function handle(SheetPusher $pusher, Spreadsheet $spreadsheet, Writer $writer)
    {
        $spreadsheet->sheets()->each(function (TranslationsSheet $translationsSheet) use ($pusher, $spreadsheet, $writer) {

            $this->output->writeln("<comment>Scanning local languages files for sheet [{$translationsSheet->getTitle()}]</comment>");

            $pusher = $pusher->setTranslationsSheet($translationsSheet);

            $translations = Util::keyValues(
                $pusher->getScannedAndTransformedTranslations(),
                $spreadsheet->getCamelizedHeader()
            );

            $this->output->writeln('<comment>.... Rewriting</comment>');
            $writer
                ->setTranslationsSheet($translationsSheet)
                ->setTranslations($translations)
                ->withOutput($this->output)
                ->write();

            $translationsSheet->api()->reset();
        });

        $this->output->writeln('<info>Done.</info>');
    }
}

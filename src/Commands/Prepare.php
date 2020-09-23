<?php

namespace Nikaia\TranslationSheet\Commands;

use Illuminate\Console\Command;
use Nikaia\TranslationSheet\SheetPusher;
use Nikaia\TranslationSheet\Sheet\TranslationsSheet;
use Nikaia\TranslationSheet\Spreadsheet;
use Nikaia\TranslationSheet\Translation\Writer;
use Nikaia\TranslationSheet\Util;

class Prepare extends Command
{
    protected $signature = 'translation_sheet:prepare';

    protected $description = 'Rewrite locales languages files by removing comments and sorting keys. This most likely reduce and simplify conflicts when pulling translations from spreadsheet.';

    public function handle(SheetPusher $pusher, Spreadsheet $spreadsheet, Writer $writer)
    {
        $spreadsheet->sheets()->each(function (TranslationsSheet $translationsSheet) use ($pusher, $spreadsheet, $writer) {

            $this->output->writeln("<comment>Scanning local languages files for sheet [{$translationsSheet->getTitle()}]</comment>");

            $pusher = $pusher->setTranslationSheet($translationsSheet);

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
        });

        $this->output->writeln('<info>Done.</info>');
    }
}

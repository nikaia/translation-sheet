<?php

namespace Nikaia\TranslationSheet\Commands;

use Illuminate\Console\Command;
use Nikaia\TranslationSheet\Sheet\TranslationsSheet;
use Nikaia\TranslationSheet\Spreadsheet;

class Setup extends Command
{
    protected $signature = 'translation_sheet:setup';

    protected $description = 'Setup spreadsheet and get it ready to host translations';

    public function handle(Spreadsheet $spreadsheet)
    {
        $spreadsheet->ensureConfiguredSheetsAreCreated();

        $spreadsheet->sheets()->each(function (TranslationsSheet $translationsSheet) {
            $this->output->writeln(
                '<comment>Setting up translations sheet [' . $translationsSheet->getTitle() . ']</comment>'
            );

            $translationsSheet->api()->addBatchRequests(
                $translationsSheet->api()->setTabColor($translationsSheet->getId(), $translationsSheet->getTabColor())
            );
        });

        $spreadsheet->api()->sendBatchRequests();

        $this->output->writeln('<info>Done. Spreasheet is ready.</info>');
    }
}

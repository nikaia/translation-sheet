<?php


namespace Nikaia\TranslationSheet\Commands;

use Illuminate\Console\Command;
use Nikaia\TranslationSheet\Sheet\TranslationsSheet;

class Status extends Command
{
    protected $signature = 'translation_sheet:status';

    protected $description = 'Display the status of translations : Locked / Unlocked.';

    public function handle(TranslationsSheet $translationsSheet)
    {
        $locked = $translationsSheet->isTranslationsLocked();

        $label = $locked ? 'LOCKED' : 'UNLOCKED';
        $style = $locked ? 'error' : 'info';

        $this->line("Translations area is <$style>$label</$style>");
    }
}

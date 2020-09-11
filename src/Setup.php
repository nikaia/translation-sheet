<?php

namespace Nikaia\TranslationSheet;

use Nikaia\TranslationSheet\Commands\Output;
use Nikaia\TranslationSheet\Sheet\TranslationsSheet;

class Setup
{
    use Output;

    /** @var TranslationsSheet */
    protected $translationsSheet;

    public function __construct(TranslationsSheet $translationsSheet)
    {
        $this->translationsSheet = $translationsSheet;

        $this->nullOutput();
    }

    public function run()
    {
        $this->output->writeln('<comment>Setting up default translations sheet</comment>');
        $this->translationsSheet->setup();

        $this->output->writeln('<info>Done. Spreasheet is ready.</info>');
    }
}

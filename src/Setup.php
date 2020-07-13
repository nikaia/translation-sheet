<?php

namespace Nikaia\TranslationSheet;

use Nikaia\TranslationSheet\Commands\Output;
use Nikaia\TranslationSheet\Sheet\MetaSheet;
use Nikaia\TranslationSheet\Sheet\TranslationsSheet;

class Setup
{
    use Output;

    /** @var TranslationsSheet */
    protected $translationsSheet;

    /** @var MetaSheet */
    protected $metaSheet;

    public function __construct(TranslationsSheet $translationsSheet, MetaSheet $metaSheet)
    {
        $this->translationsSheet = $translationsSheet;
        $this->metaSheet = $metaSheet;

        $this->nullOutput();
    }

    public function run()
    {
        $this->output->writeln('<comment>Setting up Translations sheet</comment>');
        $this->translationsSheet
            ->setTitle(config('translation_sheet.default_tab_name', 'Translations'))
            ->setup();

        foreach (config('translation_sheet.additional_tabs') as $tab) {
            $this->output->writeln('<comment>Setting up '. $tab['name'] .' sheet</comment>');
            $this->translationsSheet
                ->setTitle($tab['name'])
                ->setup();
        }

        $this->output->writeln('<comment>Adding Meta sheet</comment>');
        $this->metaSheet->setup();

        $this->output->writeln('<info>Done. Spreasheet is ready.</info>');
    }
}

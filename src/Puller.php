<?php

namespace Nikaia\TranslationSheet;

use Nikaia\TranslationSheet\Commands\Output;
use Nikaia\TranslationSheet\Sheet\TranslationsSheet;
use Nikaia\TranslationSheet\Translation\Writer;

class Puller
{
    use Output;

    /** @var TranslationsSheet */
    protected $translationsSheet;

    /** @var Writer */
    protected $writer;

    public function __construct(TranslationsSheet $translationsSheet, Writer $writer)
    {
        $this->translationsSheet = $translationsSheet;
        $this->writer = $writer;

        $this->nullOutput();
    }

    public function pull()
    {
        $this->pullTab(config('translation_sheet.default_tab_name', 'Translations'));

        foreach (config('translation_sheet.additional_tabs') as $tab) {
            $this->pullTab($tab['name'], $tab['output_format'], $tab['path']);
        }

        $this->output->writeln('<info>Done.</info>');
    }

    public function pullTab($title, $outputFormat = null, $path = null)
    {
        $this->output->writeln('<comment>Pulling '. $title .' translations from Spreadsheet</comment>');
        $translations = $this->getTranslations($title);

        $this->output->writeln('<comment>Writing languages files :</comment>');
        $this->writer
            ->withOutput($this->output)
            ->setTranslations($translations)
            ->setFormat($outputFormat)
            ->setOutputPath($path)
            ->write();
    }

    public function getTranslations($title = null)
    {
        $header = $this->translationsSheet->getSpreadsheet()->getCamelizedHeader();
        if ($title) {
            $this->translationsSheet->setTitle($title);
        }

        $translations = $this->translationsSheet->readTranslations();

        return Util::keyValues($translations, $header);
    }
}

<?php

namespace Nikaia\TranslationSheet;

use Nikaia\TranslationSheet\Commands\Output;
use Nikaia\TranslationSheet\Sheet\TranslationsSheet;
use Nikaia\TranslationSheet\Translation\Writer;

class SheetPuller
{
    use Output;

    /** @var TranslationsSheet */
    protected $translationsSheet;

    /** @var Writer */
    protected $writer;

    public function __construct(Writer $writer)
    {
        $this->writer = $writer;

        $this->nullOutput();
    }

    public function setTranslationsSheet(TranslationsSheet $translationsSheet)
    {
        $this->translationsSheet = $translationsSheet;

        return $this;
    }

    public function pull()
    {
        $this->output->writeln('<comment>Pulling translation from Spreadsheet</comment>');
        $translations = $this->getTranslations();

        $this->output->writeln('<comment>Writing languages files :</comment>');
        $this->writer
            ->withOutput($this->output)
            ->setTranslationsSheet($this->translationsSheet)
            ->setTranslations($translations)
            ->write();

        $this->output->writeln('<info>Done.</info>');
    }

    public function getTranslations()
    {
        $header = $this->translationsSheet->getSpreadsheet()->getCamelizedHeader();

        $translations = $this->translationsSheet->readTranslations();

        return Util::keyValues($translations, $header);
    }
}

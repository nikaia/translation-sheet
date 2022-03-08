<?php

namespace Nkreliefdev\TranslationSheet;

use Nkreliefdev\TranslationSheet\Commands\Output;
use Nkreliefdev\TranslationSheet\Sheet\TranslationsSheet;
use Nkreliefdev\TranslationSheet\Translation\Writer;

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
        $this->output->writeln("Pulling translations from sheet [<comment>{$this->translationsSheet->getTitle()}</comment>] :");
        $translations = $this->getTranslations();

        $this->output->writeln('    <comment>Writing languages files :</comment>');
        $this->writer
            ->withOutput($this->output)
            ->setTranslationsSheet($this->translationsSheet)
            ->setTranslations($translations)
            ->write();

        $this->output->writeln('    <info>Done.</info>');
        $this->output->writeln(PHP_EOL);
    }

    public function getTranslations()
    {
        $header = $this->translationsSheet->getSpreadsheet()->getCamelizedHeader();

        $translations = $this->translationsSheet->readTranslations();

        return Util::keyValues($translations, $header);
    }
}

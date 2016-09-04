<?php

namespace Nikaia\TranslationSheet;

use Nikaia\TranslationSheet\Commands\Output;
use Nikaia\TranslationSheet\Sheet\TranslationsSheet;
use Nikaia\TranslationSheet\Translation\Reader;
use Nikaia\TranslationSheet\Translation\Transformer;

class Pusher
{
    use Output;

    /** @var Reader */
    protected $reader;

    /** @var TranslationsSheet */
    protected $translationsSheet;

    /** @var Transformer */
    protected $transformer;

    public function __construct(Reader $reader, TranslationsSheet $translationsSheet, Transformer $transformer)
    {
        $this->reader = $reader;
        $this->translationsSheet = $translationsSheet;
        $this->transformer = $transformer;

        $this->nullOutput();
    }

    public function push()
    {
        $this->output->writeln('<comment>Scanning languages files</comment>');
        $translations = $this->getScannedAndTransformedTranslations();

        $this->output->writeln('<comment>Preparing spreasheet for new write operation</comment>');
        $this->translationsSheet->prepareForWrite();

        $this->output->writeln('<comment>Writing translations in the spreadsheet</comment>');
        $this->translationsSheet->writeTranslations($translations->toArray());

        $this->output->writeln('<comment>Styling document</comment>');
        $this->translationsSheet->styleDocument();

        $this->output->writeln('<info>Done</info>.');
    }

    public function getScannedAndTransformedTranslations()
    {
        return $this->transformer
            ->setLocales($this->translationsSheet->getSpreadsheet()->getLocales())
            ->transform($this->reader->scan());
    }
}

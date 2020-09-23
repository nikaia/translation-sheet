<?php

namespace Nikaia\TranslationSheet;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Nikaia\TranslationSheet\Commands\Output;
use Nikaia\TranslationSheet\Sheet\TranslationsSheet;
use Nikaia\TranslationSheet\Translation\Reader;
use Nikaia\TranslationSheet\Translation\Transformer;

class SheetPusher
{
    use Output;

    /** @var Reader */
    protected $reader;

    /** @var TranslationsSheet */
    protected $translationSheet;

    /** @var Transformer */
    protected $transformer;

    public function __construct(Reader $reader, Transformer $transformer)
    {
        $this->reader = $reader;
        $this->transformer = $transformer;

        $this->nullOutput();
    }

    public function setTranslationSheet(TranslationsSheet $translationsSheet)
    {
        $this->translationSheet = $translationsSheet;

        $this->reader->setTranslationsSheet($translationsSheet);
        $this->transformer->setTranslationsSheet($translationsSheet);

        return $this;
    }

    public function push()
    {
        $this->output->writeln('<comment>Scanning languages files</comment>');
        $translations = $this->getScannedAndTransformedTranslations();

        $this->output->writeln('<comment>Preparing spreadsheet for new write operation</comment>');
        $this->translationSheet->prepareForWrite();

        $this->output->writeln('<comment>Updating header</comment>');
        $this->translationSheet->updateHeaderRow();

        $this->output->writeln('<comment>Writing translations in the spreadsheet</comment>');
        $this->translationSheet->writeTranslations($translations->all());

        $this->output->writeln('<comment>Styling document</comment>');
        $this->translationSheet->styleDocument();

        $this->output->writeln('<info>Done</info>.');
    }

    public function getScannedAndTransformedTranslations()
    {
        $excludePatterns = config('translation_sheet.exclude');

        return $this->transformer
            ->setLocales($this->translationSheet->getSpreadsheet()->getLocales())
            ->transform($this->reader->scan())
            ->when(is_array($excludePatterns) && !empty($excludePatterns), function (Collection $collection) use ($excludePatterns) {
                return $collection->reject(function ($item) use ($excludePatterns) {
                    return Str::is($excludePatterns, $item[0] /* full key */);
                })->values();
            });
    }
}

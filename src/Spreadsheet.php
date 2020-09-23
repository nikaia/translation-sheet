<?php

namespace Nikaia\TranslationSheet;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Nikaia\TranslationSheet\Client\Api;
use Nikaia\TranslationSheet\Sheet\Styles;
use Nikaia\TranslationSheet\Sheet\TranslationsSheet;
use Nikaia\TranslationSheet\Sheet\TranslationsSheetCoordinates;

class Spreadsheet
{
    /** @var string */
    protected $id;

    /** @var array */
    protected $locales = [];

    /** @var array */
    protected $translations = [];

    /** @var Api */
    protected $api;

    public function __construct($id, $locales, $api = null)
    {
        $this->id = $id;
        $this->locales = $locales;
        $this->api = $api ?: app(Api::class);
    }

    public function setLocales($locales)
    {
        $this->locales = $locales;

        return $this;
    }

    public function setTranslations($translations)
    {
        $this->translations = $translations;

        return $this;
    }

    public function getTranslations()
    {
        return $this->translations;
    }

    public function getTranslationsCount()
    {
        return count($this->translations);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUrl()
    {
        return 'https://docs.google.com/spreadsheets/d/' . $this->id;
    }

    public function getLocales()
    {
        return $this->locales;
    }

    public function getLocalesCount()
    {
        return count($this->locales);
    }

    public function getHeader()
    {
        return array_merge(
            array_merge(['Full key'], $this->getLocales()),
            ['Namespace', 'Group', 'Key', 'Source file']
        );
    }

    public function getCamelizedHeader()
    {
        return array_map(function ($item) {
            if (in_array($item, Util::asArray($this->getLocales()))) {
                return $item;
            }

            return Str::camel($item);
        }, $this->getHeader());
    }

    public function getHeaderColumnsCount()
    {
        $header = $this->getHeader();

        return count($header);
    }

    public function getHeaderRowsCount()
    {
        return 1;
    }

    /**
     * @param $sheetId
     * @param $sheetTitle
     *
     * @return TranslationsSheetCoordinates
     */
    public function translationsEmptySheetCoordinates($sheetId, $sheetTitle)
    {
        return TranslationsSheetCoordinates::emptySheet(
            $this->getHeaderColumnsCount(),
            $this->getLocalesCount(),
            $this->getHeaderColumnsCount()
        )->setSheetId($sheetId)->setSheetTitle($sheetTitle);
    }

    /**
     * @param $sheetId
     * @param $sheetTitle
     * @return TranslationsSheetCoordinates
     */
    public function translationsSheetCoordinates($sheetId, $sheetTitle)
    {
        return TranslationsSheetCoordinates::sheetWithData(
            $this->getTranslationsCount(),
            $this->getHeaderColumnsCount(),
            $this->getLocalesCount(),
            $this->getHeaderRowsCount()
        )->setSheetId($sheetId)->setSheetTitle($sheetTitle);
    }

    public function sheetStyles()
    {
        return new Styles;
    }

    public function deleteAllSheets()
    {
        // We need to create a black sheet, cause we cannot delete all sheets
        $this->api()
            ->addBatchRequests([
                $this->api()->addBlankSheet()
            ])
            ->sendBatchRequests();

        // Delete all sheet and keep the last created one
        $this->api()
            ->addBatchRequests(
                collect($this->api()->freshSheets())->slice(0, -1)->map(function ($sheet) {
                    return $this->api()->deleteSheetRequest($sheet['properties']['sheetId']);
                })->toArray()
            )
            ->sendBatchRequests();

        $this->api()->forgetSheets();
    }

    /**
     * Return api instance initialized with the spreadsheet ID.
     *
     * @return Api
     */
    public function api()
    {
        return $this->api->setSpreadsheetId($this->getId());
    }

    /**
     * Returns collection of the spreadsheets represented by TranslationSheet objects.
     *
     * @return Collection
     */
    public function sheets()
    {
        $this->api()->forgetSheets()->getSheets();

        return $this->configuredSheets()->map(function (TranslationsSheet $translationsSheet) {
            if ($sheet = $this->api()->getSheetByTitle($translationsSheet->getTitle())) {
                $translationsSheet->setId(
                    data_get($sheet, 'properties.sheetId')
                );
            }

            return $translationsSheet;
        });
    }

    public function configuredSheets()
    {
        return collect([static::primaryTranslationSheet()])
            ->merge(
                collect(config('translation_sheet.extra_sheets'))
                    ->map(function ($sheetConfig) {
                        /** @var TranslationsSheet $instance */
                        $instance = resolve(TranslationsSheet::class);

                        return $instance
                            ->markAsExtraSheet()
                            ->setTitle($sheetConfig['name'])
                            ->setFormat($sheetConfig['format'])
                            ->setPath($sheetConfig['path'])
                            ->setTabColor($sheetConfig['tabColor']);
                    })
            );
    }

    public function configuredExtraSheets()
    {
        // Remove the first one. (aka. the translations required default sheet).
        return $this->configuredSheets()->slice(1);
    }

    public function ensureConfiguredSheetsAreCreated()
    {
        $googleSheets = collect($this->api()->forgetSheets()->getSheets());
        $configuredSheets = $this->configuredSheets();

        // Configured sheets are already created
        if (count($googleSheets) === $configuredSheets->count()) {
            return;
        }

        // This case is not supposed to happen. just return.
        // Or user messed up manually the spreadsheet by creating sheet, maybe?!
        if (count($googleSheets) > $configuredSheets->count()) {
            return;
        }

        // By default, spreadsheet has 1 required sheet. This will match translation-sheet
        // default translations sheet.
        $requests = [
            $this->api()->setSheetTitle(
                $firstSheetId = data_get(collect($googleSheets)->first(), 'properties.sheetId'),
                config('translation_sheet.primary_sheet.name', 'Translations')
            )
        ];

        // Then, we need to create any extra sheets before we can use them.
        $this->configuredExtraSheets()->each(function (TranslationsSheet $translationsSheet) use (&$requests) {
            if ($this->api()->getSheetByTitle($translationsSheet->getTitle())) {
                return;
            }

            $requests[] = $this->api()->addBlankSheet($translationsSheet->getTitle());
        });

        $this->api()->addBatchRequests($requests)->sendBatchRequests();
    }

    public static function primaryTranslationSheet()
    {
        /** @var TranslationsSheet $instance */
        $instance = resolve(TranslationsSheet::class);

        return $instance
            ->markAsPrimarySheet()
            ->setTitle(config('translation_sheet.primary_sheet.name', 'Translations'));
    }

}

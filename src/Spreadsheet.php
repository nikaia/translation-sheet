<?php

namespace Nikaia\TranslationSheet;

use Illuminate\Support\Str;
use Nikaia\TranslationSheet\Client\Api;
use Nikaia\TranslationSheet\Sheet\Styles;
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

    protected $columnsCount;

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

    public function getTitle()
    {
        return 'Translations';
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

    public function getColumnsCount()
    {
        if (! $this->columnsCount) {
            $this->columnsCount = $this->getHeaderColumnsCount();
        }

        return $this->columnsCount;
    }

    public function setColumnsCount($count)
    {
        $this->columnsCount = $count;
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
            $this->getColumnsCount(),
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
            $this->getColumnsCount(),
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
                collect($this->api()->getSheets())->slice(0, -1)->map(function ($sheet) {
                    return $this->api()->deleteSheetRequest($sheet['properties']['sheetId']);
                })
                ->toArray()
            )
            ->sendBatchRequests();
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
}

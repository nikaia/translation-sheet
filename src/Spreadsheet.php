<?php

namespace Nikaia\TranslationSheet;

use Illuminate\Support\Str;
use Nikaia\TranslationSheet\Util;
use Nikaia\TranslationSheet\Client\Api;
use Nikaia\TranslationSheet\Sheet\Styles;
use Nikaia\TranslationSheet\Sheet\TranslationsSheetCoordinates;

class Spreadsheet
{
    /** @var string */
    protected $id;

    /** @var array */
    protected $locales;

    /** @var array */
    protected $translations;

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
        return 'https://docs.google.com/spreadsheets/d/'.$this->id;
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

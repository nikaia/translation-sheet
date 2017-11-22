<?php

namespace Nikaia\TranslationSheet\Sheet;

class TranslationsSheetCoordinates
{
    protected $headerRowsCount = 1;

    protected $dataRowsCount = 0;

    protected $columnsCount = 0;

    protected $localesCount = 1;

    protected $sheetId;

    protected $sheetTitle;

    private function __construct()
    {
    }

    public function setSheetId($sheetId)
    {
        $this->sheetId = $sheetId;

        return $this;
    }

    public function setSheetTitle($sheetTitle)
    {
        $this->sheetTitle = $sheetTitle;

        return $this;
    }

    public static function emptySheet($columnsCount, $localesCount, $headerRowsCount)
    {
        $instance = new self;

        $instance->headerRowsCount = $headerRowsCount;
        $instance->dataRowsCount = 10;
        $instance->columnsCount = $columnsCount;
        $instance->localesCount = $localesCount;

        return $instance;
    }

    public static function sheetWithData($dataRowsCount, $columnsCount, $localesCount, $headerRowsCount)
    {
        $instance = new self;

        $instance->headerRowsCount = $headerRowsCount;
        $instance->dataRowsCount = $dataRowsCount;
        $instance->columnsCount = $columnsCount;
        $instance->localesCount = $localesCount;

        return $instance;
    }

    public function headerShortRange()
    {
        $alphabet = $this->returnRanges();

        return $this->sheetTitle.'!A1:'.$alphabet[$this->getColumnsCount() - 1].'1';
    }

    public function headerRange()
    {
        return [
            'sheetId' => $this->sheetId,
            'startRowIndex' => 0,
            'endRowIndex' => 1,
            'startColumnIndex' => 0,
            'endColumnIndex' => $this->getColumnsCount(),
        ];
    }

    public function fullKeyColumnRange()
    {
        return [
            'sheetId' => $this->sheetId,
            'startRowIndex' => 1,
            'endRowIndex' => $this->getRowsCount(),
            'startColumnIndex' => 0,
            'endColumnIndex' => 1,
        ];
    }

    public function metaColumnsRange()
    {
        return [
            'sheetId' => $this->sheetId,
            'startRowIndex' => 1,
            'endRowIndex' => $this->getRowsCount(),
            'startColumnIndex' => $this->getLocalesCount() + 1,
            'endColumnIndex' => $this->getColumnsCount(),
        ];
    }

    public function dataShortRange($firstRow = 2, $noLastRow = false)
    {
        $alphabet = $this->returnRanges();
        $firstColumn = 'A';
        $lastColumn = $alphabet[$this->getColumnsCount() - 1];
        $lastRow = $this->getRowsCount();

        return $this->sheetTitle.'!'.$firstColumn.$firstRow.':'.$lastColumn.($noLastRow ? '' : $lastRow);
    }

    public function dataRange($endRow, $firstRow = 2)
    {
        return [
            'sheetId' => $this->sheetId,
            'startRowIndex' => $firstRow,
            'endRowIndex' => $endRow,
            'startColumnIndex' => 0,
            'endColumnIndex' => $this->getColumnsCount(),
        ];
    }

    public function translationsRange($firstColumn = 1, $rowCount = null)
    {
        return [
            'sheetId' => $this->sheetId,
            'startRowIndex' => 1,
            'endRowIndex' => $rowCount ?: $this->getRowsCount(),
            'startColumnIndex' => $firstColumn,
            'endColumnIndex' => $firstColumn + $this->getLocalesCount(),
        ];
    }

    public function getColumnsCount()
    {
        return $this->columnsCount;
    }

    public function getRowsCount()
    {
        return $this->dataRowsCount + $this->headerRowsCount;
    }

    public function getLocalesCount()
    {
        return $this->localesCount;
    }

    public function namespaceColumnIndex()
    {
        return $this->getLocalesCount() + 1;
    }

    public function groupColumnIndex()
    {
        return $this->getLocalesCount() + 2;
    }

    public function keyColumnIndex()
    {
        return $this->getLocalesCount() + 3;
    }

    public function sourceFileColumnIndex()
    {
        return $this->getLocalesCount() + 4;
    }

    public function returnRanges()
    {
        // Adding support for a bigger columns range fx. AA-ZZ columns
        for ($x = 'A'; $x !== 'ZZ'; $x++) {
            $ranges[] = $x;
        }

        return $ranges;
    }
}

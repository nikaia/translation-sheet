<?php

namespace Nikaia\TranslationSheet\Sheet;

class TranslationsSheet extends AbstractSheet
{
    public function getId()
    {
        return 0;
    }

    public function getTitle()
    {
        return 'Translations';
    }

    public function coordinates()
    {
        return $this->spreadsheet->translationsSheetCoordinates($this->getId(), $this->getTitle());
    }

    public function emptyCoordinates()
    {
        return $this->spreadsheet->translationsSheetCoordinates($this->getId(), $this->getTitle());
    }

    public function setup()
    {
        $this->spreadsheet->api()
            ->addBatchRequests([
                // Set properties
                $this->spreadsheet->api()->setSheetPropertiesRequest($this->getId(), $this->getTitle(), $this->styles()->translationSheetTabColor()),
            ])
            ->sendBatchRequests();
    }

    public function writeTranslations($translations)
    {
        $this->spreadsheet->setTranslations($translations);

        $this->spreadsheet->api()
            ->writeCells($this->coordinates()->dataShortRange(), $translations);
    }

    public function readTranslations()
    {
        return $this->spreadsheet->api()->readCells($this->getId(), $this->coordinates()->dataShortRange(2, true));
    }

    public function styleDocument()
    {
        $fullkeyRange = $this->coordinates()->fullKeyColumnRange();
        $translationsRange = $this->coordinates()->translationsRange();
        $metaRange = $this->coordinates()->metaColumnsRange();

        $requests = [
            // Header row
            $this->spreadsheet->api()->frozenRowRequest($this->getId()),
            $this->spreadsheet->api()->styleArea($this->emptyCoordinates()->headerRange(), $this->styles()->translationsHeader()),
            $this->spreadsheet->api()->protectRangeRequest($this->emptyCoordinates()->headerRange(), 'HEADER.'),

            // Full key column
            $this->spreadsheet->api()->frozenColumnRequest($this->getId()),
            $this->spreadsheet->api()->protectRangeRequest($fullkeyRange, 'FULL_KEY'),
            $this->spreadsheet->api()->fixedColumnWidthRequest($this->getId(), 0, 1, 450),
            $this->spreadsheet->api()->styleArea($fullkeyRange, $this->styles()->fullKeyColumn()),

            // Translations columns
            $this->spreadsheet->api()->styleArea($translationsRange, $this->styles()->translationsColumns()),

            // Meta columns
            $this->spreadsheet->api()->protectRangeRequest($metaRange, 'META'),
            $this->spreadsheet->api()->styleArea($metaRange, $this->styles()->metaColumns()),
            $this->spreadsheet->api()->fixedColumnWidthRequest($this->getId(), $this->coordinates()->namespaceColumnIndex(), $this->coordinates()->namespaceColumnIndex() + 1, 145),
            $this->spreadsheet->api()->fixedColumnWidthRequest($this->getId(), $this->coordinates()->groupColumnIndex(), $this->coordinates()->groupColumnIndex() + 1, 80),
            $this->spreadsheet->api()->fixedColumnWidthRequest($this->getId(), $this->coordinates()->keyColumnIndex(), $this->coordinates()->keyColumnIndex() + 1, 240),
            $this->spreadsheet->api()->fixedColumnWidthRequest($this->getId(), $this->coordinates()->sourceFileColumnIndex(), $this->coordinates()->sourceFileColumnIndex() + 1, 360),

            // Delete extra columns and rows
            $this->spreadsheet->api()->deleteRowsFrom($this->getId(), $this->coordinates()->getRowsCount()),
            $this->spreadsheet->api()->deleteColumnsFrom($this->getId(), $this->coordinates()->getColumnsCount()),
        ];

        // Fixed locales translations column width
        $beginAt = 1;
        foreach ($this->spreadsheet->getLocales() as $locale) {
            $requests[] = $this->spreadsheet->api()->fixedColumnWidthRequest($this->getId(), $beginAt, $beginAt + 1, 350);
            $beginAt++;
        }

        // Send requests
        $this->spreadsheet->api()->addBatchRequests($requests)->sendBatchRequests();
    }

    public function prepareForWrite()
    {
        // We need to remove all protected ranges, to avoid any duplicates that may lead to
        // some weird behaviours
        $this->removeAllProtectedRanges();
    }

    public function lockTranslations()
    {
        $range = $this->coordinates()->translationsRange(1, $this->spreadsheet->api()->getSheetRowCount($this->getId()));

        $this->api()
            ->addBatchRequests([
                $this->spreadsheet->api()->protectRangeRequest($range, 'TRANSLATIONS'),
                $this->spreadsheet->api()->styleArea($range, $this->styles()->lockedTranslationsColumns()),
            ])
            ->sendBatchRequests();
    }

    public function unlockTranslations()
    {
        $range = $this->coordinates()->translationsRange(1, $this->spreadsheet->api()->getSheetRowCount($this->getId()));

        $protectedRanges = $this->spreadsheet->api()->getSheetProtectedRanges($this->getId(), 'TRANSLATIONS');
        foreach ($protectedRanges as $protectedRange) {
            $requests[] = $this->spreadsheet->api()->deleteProtectedRange($protectedRange->protectedRangeId);
        }

        $requests[] = $this->spreadsheet->api()->styleArea($range, $this->styles()->translationsColumns());

        $this->spreadsheet->api()->addBatchRequests($requests)->sendBatchRequests();
    }

    public function isTranslationsLocked()
    {
        $protectedRanges = $this->spreadsheet->api()->getSheetProtectedRanges($this->getId(), 'TRANSLATIONS');

        return ! empty($protectedRanges) && count($protectedRanges) > 0;
    }

    public function removeAllProtectedRanges()
    {
        $protectedRanges = $this->spreadsheet->api()->getSheetProtectedRanges($this->getId());

        $requests = [];
        foreach ($protectedRanges as $protectedRange) {
            $requests[] = $this->spreadsheet->api()->deleteProtectedRange($protectedRange->protectedRangeId);
        }

        if (! empty($requests)) {
            $this->spreadsheet->api()->addBatchRequests($requests)->sendBatchRequests();
        }
    }
}

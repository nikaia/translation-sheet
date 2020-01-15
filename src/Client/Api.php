<?php

namespace Nikaia\TranslationSheet\Client;

use Google_Service_Sheets;
use Google_Service_Sheets_BatchUpdateSpreadsheetRequest;
use Google_Service_Sheets_BatchUpdateValuesRequest;
use Google_Service_Sheets_Request;
use Illuminate\Support\Collection;

class Api
{
    /** @var Collection */
    protected $requests;

    /** @var Client */
    protected $client;

    /** @var string */
    protected $spreadsheetId;

    /**
     * SheetService constructor.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->requests = new Collection([]);
    }

    /**
     * Set the service speadsheet ID.
     *
     * @param string $spreadsheetId
     * @return $this
     */
    public function setSpreadsheetId($spreadsheetId)
    {
        $this->spreadsheetId = $spreadsheetId;

        return $this;
    }

    public function addBatchRequests($requests)
    {
        $requests = is_array($requests) ? $requests : [$requests];
        foreach ($requests as $request) {
            $this->requests->push($request);
        }

        return $this;
    }

    public function sendBatchRequests()
    {
        $sheets = new Google_Service_Sheets($this->client);

        $sheets->spreadsheets->batchUpdate(
            $this->spreadsheetId,
            new Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
                'requests' => $this->requests->toArray(),
            ])
        );

        $this->requests = new Collection([]);

        return $this;
    }

    public function frozenRowRequest($sheetId, $frozonRowCount = 1)
    {
        return new Google_Service_Sheets_Request([
            'updateSheetProperties' => [
                'properties' => [
                    'sheetId' => $sheetId,
                    'gridProperties' => [
                        'frozenRowCount' => $frozonRowCount,
                    ],
                ],
                'fields' => 'gridProperties.frozenRowCount',
            ],
        ]);
    }

    public function frozenColumnRequest($sheetId, $frozonColumnCount = 1)
    {
        return new Google_Service_Sheets_Request([
            'updateSheetProperties' => [
                'properties' => [
                    'sheetId' => $sheetId,
                    'gridProperties' => [
                        'frozenColumnCount' => $frozonColumnCount,
                    ],
                ],
                'fields' => 'gridProperties.frozenColumnCount',
            ],
        ]);
    }

    public function styleArea($range, $styles)
    {
        return new Google_Service_Sheets_Request([
            'repeatCell' => [
                'range' => $range,
                'cell' => [
                    'userEnteredFormat' => [
                        'backgroundColor' => $this->fractalColors($styles['backgroundColor']),
                        'horizontalAlignment' => $styles['horizontalAlignment'],
                        'verticalAlignment' => $styles['verticalAlignment'],
                        'textFormat' => [
                            'foregroundColor' => $this->fractalColors($styles['foregroundColor']),
                            'fontSize' => $styles['fontSize'],
                            'fontFamily' => $styles['fontFamily'],
                            'bold' => $styles['bold'],
                        ],
                    ],
                ],
                'fields' => 'userEnteredFormat(backgroundColor,textFormat,horizontalAlignment)',
            ],
        ]);
    }

    public function fixedColumnWidthRequest($sheetId, $startIndex, $endIndex, $width)
    {
        return new Google_Service_Sheets_Request([
            'updateDimensionProperties' => [
                'range' => [
                    'sheetId' => $sheetId,
                    'dimension' => 'COLUMNS',
                    'startIndex' => $startIndex,
                    'endIndex' => $endIndex,
                ],
                'properties' => [
                    'pixelSize' => $width,
                ],
                'fields' => 'pixelSize',
            ],
        ]);
    }

    public function setSheetPropertiesRequest($sheetId, $title, $tabColor)
    {
        return new Google_Service_Sheets_Request([
            'updateSheetProperties' => [
                'properties' => [
                    'sheetId' => $sheetId,
                    'title' => $title,
                    'tabColor' => $this->fractalColors($tabColor),
                ],
                'fields' => 'title,tabColor',
            ],
        ]);
    }

    public function addSheetRequest($title, $rowCount, $columnCount, $tabColor = null)
    {
        $properties = [
            'title' => $title,
            'gridProperties' => [
                'rowCount' => $rowCount,
                'columnCount' => $columnCount,
            ],
        ];

        if (!is_null($tabColor)) {
            $properties['tabColor'] = $this->fractalColors($tabColor);
        }

        return new Google_Service_Sheets_Request(['addSheet' => ['properties' => $properties]]);
    }

    public function addBlankSheet($title = null)
    {
        $properties = $title ? ['title' => $title] : [];
        return new Google_Service_Sheets_Request(['addSheet' => ['properties' => $properties]]);
    }

    public function clearSheetRequest($sheetId)
    {
        return new Google_Service_Sheets_Request([
            'updateCells' => [
                'range' => [
                    'sheetId' => $sheetId,
                ],
                'fields' => 'userEnteredValue',
            ],
        ]);
    }

    public function protectRangeRequest($range, $description)
    {
        return new Google_Service_Sheets_Request([
            'addProtectedRange' => [
                'protectedRange' => [
                    'range' => $range,
                    'description' => $description,
                    'warningOnly' => false,
                    'editors' => [
                        'users' => [config('translation_sheet.serviceAccountEmail')],
                    ],
                ],
            ],

        ]);
    }

    public function writeCells($shortRange, $values)
    {
        $sheets = new \Google_Service_Sheets($this->client);

        $request = new Google_Service_Sheets_BatchUpdateValuesRequest();
        $request->setValueInputOption('RAW');
        $request->setData([
            'range' => $shortRange,
            'majorDimension' => 'ROWS',
            'values' => $values,
        ]);

        $sheets->spreadsheets_values->batchUpdate($this->spreadsheetId, $request);
    }

    public function readCells($sheetId, $range)
    {
        $range .= $this->getSheetRowCount($sheetId);

        $sheets = new \Google_Service_Sheets($this->client);

        return $sheets->spreadsheets_values->get($this->spreadsheetId, $range)->values;
    }

    public function getSheetRowCount($sheetId)
    {
        $sheet = $this->getSheet($sheetId);

        return $sheet['properties']['gridProperties']['rowCount'];
    }

    public function getSheet($sheetId)
    {
        $sheets = $this->getSheets();

        foreach ($sheets as $sheet) {
            if ($sheet['properties']['sheetId'] === $sheetId) {
                return $sheet;
            }
        }
    }

    public function getSheets()
    {
        $service = new \Google_Service_Sheets($this->client);

        return $service->spreadsheets->get($this->spreadsheetId)->getSheets();
    }

    public function firstSheetId()
    {
        return data_get(collect($this->getSheets())->first(), 'properties.sheetId');
    }

    public function getSheetProtectedRanges($sheetId, $description = null)
    {
        $sheet = $this->getSheet($sheetId);

        if (is_null($description)) {
            return $sheet['protectedRanges'];
        }

        $ranges = [];
        foreach ($sheet['protectedRanges'] as $range) {
            if ($range->description === $description) {
                $ranges[] = $range;
            }
        }

        return $ranges;
    }

    public function deleteColumnsFrom($sheetId, $fromColumnIndex)
    {
        return new Google_Service_Sheets_Request([
            'deleteDimension' => [
                'range' => [
                    'sheetId' => $sheetId,
                    'dimension' => 'COLUMNS',
                    'startIndex' => $fromColumnIndex,
                ],
            ],
        ]);
    }

    public function deleteRowsFrom($sheetId, $fromRowIndex)
    {
        return new Google_Service_Sheets_Request([
            'deleteDimension' => [
                'range' => [
                    'sheetId' => $sheetId,
                    'dimension' => 'ROWS',
                    'startIndex' => $fromRowIndex,
                ],
            ],
        ]);
    }

    public function deleteProtectedRange($protectedRangeId)
    {
        return new Google_Service_Sheets_Request([
            'deleteProtectedRange' => [
                'protectedRangeId' => $protectedRangeId,
            ],
        ]);
    }

    public function deleteSheetRequest($sheetId)
    {
        return new Google_Service_Sheets_Request([
            'deleteSheet' => [
                'sheetId' => $sheetId,
            ],
        ]);
    }

    /**
     * Return Fractal RGB color array with r,g,b between 0 and 1.
     *
     * @param mixed $color array of RGB color ([255, 255, 255]) or hex string (#FFFFFF)
     *
     * @return array
     */
    protected function fractalColors($color)
    {
        if (is_array($color)) {
            list($red, $green, $blue) = $color;
        } else {
            list($red, $green, $blue) = sscanf($color, '#%02x%02x%02x');
        }

        return [
            'red' => round($red / 255, 1),
            'green' => round($green / 255, 1),
            'blue' => round($blue / 255, 1),
        ];
    }


}

<?php

namespace Nkreliefdev\TranslationSheet\Sheet;

use Nkreliefdev\TranslationSheet\Spreadsheet;

abstract class AbstractSheet
{
    /** @var Spreadsheet */
    protected $spreadsheet;

    public function __construct(Spreadsheet $spreadsheet)
    {
        $this->spreadsheet = $spreadsheet;
    }

    abstract public function getId();

    abstract public function getTitle();

    public function api()
    {
        return $this->spreadsheet->api();
    }

    public function styles()
    {
        return $this->spreadsheet->sheetStyles();
    }

    public function getSpreadsheet()
    {
        return $this->spreadsheet;
    }
}

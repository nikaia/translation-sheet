<?php

namespace Nikaia\TranslationSheet\Commands;

use Illuminate\Console\Command;
use Nikaia\TranslationSheet\Spreadsheet;

class Open extends Command
{
    protected $signature = 'translation_sheet:open';

    protected $description = 'Open the spreadsheet in the browser';

    public function handle(Spreadsheet $spreadsheet)
    {
        $url = $spreadsheet->getUrl();

        $this->comment('Opening spreadsheet '.$url);

        shell_exec($this->openCmd().' '.$url);
    }

    protected function openCmd()
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? 'start' : 'open';
    }
}

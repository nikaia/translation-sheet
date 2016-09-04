<?php

namespace Nikaia\TranslationSheet\Commands;

use Illuminate\Console\Command;
use Nikaia\TranslationSheet\Pusher;

class Push extends Command
{
    protected $signature = 'translation_sheet:push';

    protected $description = 'Push translation from your local languages files to the spreadsheet';

    public function handle(Pusher $pusher)
    {
        $pusher->withOutput($this->output)->push();
    }
}

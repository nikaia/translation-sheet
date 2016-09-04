<?php

namespace Nikaia\TranslationSheet\Commands;

use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

trait Output
{
    /** @var OutputInterface */
    protected $output;

    public function nullOutput()
    {
        $this->output = new NullOutput;
    }

    public function withOutput($output)
    {
        $this->output = $output;

        return $this;
    }

}

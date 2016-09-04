<?php

namespace Nikaia\TranslationSheet\Sheet;

class MetaSheet extends AbstractSheet
{
    public function getId()
    {
        return 1;
    }

    public function getTitle()
    {
        return 'Meta';
    }

    public function setup()
    {
        $this->spreadsheet->api()
            ->addBatchRequests([

                // Create the Meta sheet
                $this->api()->addSheetRequest(
                    $this->getTitle(), 10, 3, $this->styles()->metaSheetTabColor()
                ),

            ])
            ->sendBatchRequests();
    }
}

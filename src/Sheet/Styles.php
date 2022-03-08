<?php

namespace Nkreliefdev\TranslationSheet\Sheet;

class Styles
{
    public function translationsHeader()
    {
        return [
            'backgroundColor' => '#546E7A',
            'foregroundColor' => '#ffffff',
            'fontSize' => 10,
            'fontFamily' => 'Droid Serif',
            'bold' => true,
            'horizontalAlignment' => 'LEFT',
            'verticalAlignment' => 'MIDDLE',
            'hyperlinkDisplayType' => 'PLAIN_TEXT',
        ];
    }

    public function fullKeyColumn()
    {
        return [
            'backgroundColor' => '#E1F5FE',
            'foregroundColor' => '#9E9E9E',
            'fontSize' => 9,
            'fontFamily' => 'Consolas',
            'bold' => false,
            'horizontalAlignment' => 'LEFT',
            'verticalAlignment' => 'MIDDLE',
            'hyperlinkDisplayType' => 'PLAIN_TEXT',
        ];
    }

    public function lockedTranslationsColumns()
    {
        return [
            'backgroundColor' => '#ffe1e1',
            'foregroundColor' => '#000000',
            'fontSize' => 9,
            'fontFamily' => 'Consolas',
            'bold' => false,
            'horizontalAlignment' => 'LEFT',
            'verticalAlignment' => 'MIDDLE',
            'hyperlinkDisplayType' => 'PLAIN_TEXT',
        ];
    }

    public function translationsColumns()
    {
        return [
            'backgroundColor' => '#ffffff',
            'foregroundColor' => '#000000',
            'fontSize' => 9,
            'fontFamily' => 'Droid Serif',
            'bold' => false,
            'horizontalAlignment' => 'LEFT',
            'verticalAlignment' => 'MIDDLE',
            'wrapStrategy' => 'WRAP',
            'hyperlinkDisplayType' => 'PLAIN_TEXT',
        ];
    }

    public function metaColumns()
    {
        return [
            'backgroundColor' => '#ededed',
            'foregroundColor' => '#9E9E9E',
            'fontSize' => 8,
            'fontFamily' => 'Ubuntu',
            'bold' => false,
            'horizontalAlignment' => 'LEFT',
            'verticalAlignment' => 'MIDDLE',
            'hyperlinkDisplayType' => 'PLAIN_TEXT',
        ];
    }

    public function translationSheetTabColor()
    {
        return '#546E7A';
    }

    public function metaSheetTabColor()
    {
        return [0, 188, 212];
    }
}

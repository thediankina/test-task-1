<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arComponentDescription = [
    'NAME' => Loc::getMessage('THEDIANKINA_NEWS_COMPONENT_NAME'),
    'DESCRIPTION' => Loc::getMessage('THEDIANKINA_NEWS_COMPONENT_DESCRIPTION'),
    'PATH' => [
        'ID' => 'thediankina',
        'NAME' => Loc::getMessage('THEDIANKINA_NEWS_COMPONENT_PATH_NAME'),
    ],
];

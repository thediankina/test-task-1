<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;
use lib\helpers\IblockHelper;

/**
 * @var array $arCurrentValues
 */

Loc::loadMessages(__FILE__);

$types = IblockHelper::getTypes();
$iblocks = $arCurrentValues['IBLOCK_TYPE'] ? IblockHelper::getIblocksByTypeId($arCurrentValues['IBLOCK_TYPE']) : [];

$arComponentParameters = [
    'GROUPS' => [
        'NEWS_FILTER' => [
            'NAME' => Loc::getMessage('THEDIANKINA_NEWS_FILTER_GROUP_NEWS_FILTER_NAME'),
        ],
    ],
    'PARAMETERS' => [
        'IBLOCK_TYPE' => [
            'PARENT' => 'NEWS_FILTER',
            'NAME' => Loc::getMessage('THEDIANKINA_NEWS_FILTER_PARAM_IBLOCK_TYPE_NAME'),
            'TYPE' => 'LIST',
            'VALUES' => $types,
            'REFRESH' => 'Y',
        ],
        'IBLOCK_ID' => [
            'PARENT' => 'NEWS_FILTER',
            'NAME' => Loc::getMessage('THEDIANKINA_NEWS_FILTER_PARAM_IBLOCK_ID_NAME'),
            'TYPE' => 'LIST',
            'VALUES' => $iblocks,
        ],
    ],
];

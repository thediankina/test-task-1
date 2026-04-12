<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Iblock\IblockTable;
use Bitrix\Iblock\TypeLanguageTable;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

/**
 * @var array $arCurrentValues
 */

Loc::loadMessages(__FILE__);

Loader::includeModule('iblock');

$typeLangResult = TypeLanguageTable::getList([
    'select' => ['IBLOCK_TYPE_ID', 'NAME'],
    'filter' => ['LANGUAGE_ID' => 'ru'],
    'cache' => [
        'ttl' => 3600,
        'cache_joins' => true,
    ],
]);

$types = [];
while ($type = $typeLangResult->fetchObject()) {
    $types[$type->getIblockTypeId()] = $type->getName();
}

$iblocks = [];
if (!empty($arCurrentValues['IBLOCK_TYPE'])) {
    $iblockResult = IblockTable::getList([
        'select' => ['ID', 'NAME'],
        'filter' => [
            'IBLOCK_TYPE_ID' => $arCurrentValues['IBLOCK_TYPE'],
            'ACTIVE' => 'Y',
        ],
        'order' => ['SORT' => 'ASC'],
        'cache' => [
            'ttl' => 3600,
            'cache_joins' => true,
        ],
    ]);

    while ($iblock = $iblockResult->fetchObject()) {
        $iblocks[$iblock->getId()] = $iblock->getName();
    }
}

$arComponentParameters = [
    'GROUPS' => [
        'NEWS_LIST' => [
            'NAME' => Loc::getMessage('THEDIANKINA_NEWS_GROUP_NEWS_LIST_NAME'),
        ],
    ],
    'PARAMETERS' => [
        'IBLOCK_TYPE' => [
            'PARENT' => 'NEWS_LIST',
            'NAME' => Loc::getMessage('THEDIANKINA_NEWS_PARAM_IBLOCK_TYPE_NAME'),
            'TYPE' => 'LIST',
            'VALUES' => $types,
            'REFRESH' => 'Y',
        ],
        'IBLOCK_ID' => [
            'PARENT' => 'NEWS_LIST',
            'NAME' => Loc::getMessage('THEDIANKINA_NEWS_PARAM_IBLOCK_ID_NAME'),
            'TYPE' => 'LIST',
            'VALUES' => $iblocks,
        ],
    ],
];

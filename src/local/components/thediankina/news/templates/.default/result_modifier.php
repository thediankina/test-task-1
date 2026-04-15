<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Context;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CMain $APPLICATION
 * @var CUser $USER
 * @var CBitrixComponentTemplate $this
 */

$request = Context::getCurrent()->getRequest();
$arResult['FILTER_ACTION'] = $request->getRequestUri() ?: '/';

$arResult['FILTER_FIELDS'] = [
    'NAME' => $arResult['FILTER']['NAME'] ?? '',
    'DATE_FROM' => $arResult['FILTER']['DATE_FROM'] ? date('Y.m.d', (int)strtotime($arResult['FILTER']['DATE_FROM'])) : '',
    'DATE_TO' => $arResult['FILTER']['DATE_TO'] ? date('Y.m.d', (int)strtotime($arResult['FILTER']['DATE_TO'])) : '',
    'SECTION_ID' => $arResult['FILTER']['SECTION_ID'] ?? '',
];

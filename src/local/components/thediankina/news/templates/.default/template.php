<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 */

Loc::loadMessages(__FILE__);

if (empty($arResult['ITEMS'])) {
    echo Loc::getMessage('THEDIANKINA_NEWS_ITEMS_NOT_FOUND');

    return;
}
?>
<div class='news'>
    <?php
    $APPLICATION->IncludeComponent(
        'thediankina:news.filter',
        '',
        [
            'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
            'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        ],
    );
    ?>
    <pre>
        <?php print_r($arResult) ?>
    </pre>
    <?php
    if (!empty($arResult['NAV_OBJECT'])) {
        $APPLICATION->IncludeComponent(
            'bitrix:main.pagenavigation',
            '',
            [
                'NAV_OBJECT' => $arResult['NAV_OBJECT'],
            ]
        );
    }
    ?>
</div>

<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @var array $arParams
 * @var array $arResult
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 */
?>
<div class="news">
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

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
?>
<div class="news">
    <form action="<?= $arResult['FILTER_ACTION'] ?>" method="get" class="form">
        <div class="form-group">
            <label for="name"><?= Loc::getMessage('THEDIANKINA_NEWS_FILTER_FIELD_NAME_LABEL') ?></label>
            <input type="text" name="name" id="name" value="<?= $arResult['FILTER_FIELDS']['NAME'] ?>" />
        </div>
        <div class="form-group">
            <label for="dateFrom"><?= Loc::getMessage('THEDIANKINA_NEWS_FILTER_FIELD_DATE_FROM_LABEL') ?></label>
            <input type="date" name="dateFrom" id="dateFrom" value="<?= $arResult['FILTER_FIELDS']['DATE_FROM'] ?>" />
        </div>
        <div class="form-group">
            <label for="dateTo"><?= Loc::getMessage('THEDIANKINA_NEWS_FILTER_FIELD_DATE_TO_LABEL') ?></label>
            <input type="date" name="dateTo" id="dateTo" value="<?= $arResult['FILTER_FIELDS']['DATE_TO'] ?>" />
        </div>
        <?php if (!empty($arResult['SECTIONS'])) { ?>
            <div class="form-group">
                <label for="section"><?= Loc::getMessage('THEDIANKINA_NEWS_FILTER_FIELD_SECTION_LABEL') ?></label>
                <select name="sectionId" id="section">
                    <option value=""></option>
                    <?php foreach ($arResult['SECTIONS'] as $section) { ?>
                        <option value="<?= $section['ID'] ?>" <?= (($section['ID'] == $arResult['FILTER']['SECTION_ID']) ? 'selected=""' : '') ?>>
                            <?= $section['NAME'] ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        <?php } ?>
        <div class="form-control">
            <button type="submit" class="btn btn-primary">
                <?= Loc::getMessage('THEDIANKINA_NEWS_FILTER_SUBMIT_BUTTON_LABEL') ?>
            </button>
            <button type="reset" class="btn btn-secondary">
                <?= Loc::getMessage('THEDIANKINA_NEWS_FILTER_RESET_BUTTON_LABEL') ?>
            </button>
        </div>
    </form>
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

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
<form action="/" method="get" class="form">
    <div class="form-group">
        <label for="name"><?= Loc::getMessage('THEDIANKINA_NEWS_FORM_FIELD_NAME_LABEL') ?></label>
        <input type="text" name="name" id="name" value="" />
    </div>
    <div class="form-group">
        <label for="dateFrom"><?= Loc::getMessage('THEDIANKINA_NEWS_FORM_FIELD_DATE_FROM_LABEL') ?></label>
        <input type="date" name="dateFrom" id="dateFrom" value="" />
    </div>
    <div class="form-group">
        <label for="dateTo"><?= Loc::getMessage('THEDIANKINA_NEWS_FORM_FIELD_DATE_TO_LABEL') ?></label>
        <input type="date" name="dateTo" id="dateTo" value="" />
    </div>
    <?php if (!empty($arResult['SECTIONS'])) { ?>
        <div class="form-group">
            <label for="section"><?= Loc::getMessage('THEDIANKINA_NEWS_FORM_FIELD_SECTION_LABEL') ?></label>
            <select name="sectionId" id="section">
                <option value=""></option>
                <?php foreach ($arResult['SECTIONS'] as $section) { ?>
                    <option value="<?= $section['ID'] ?>">
                        <?= $section['NAME'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>
    <?php } ?>
    <div class="form-control">
        <button type="submit" class="btn btn-primary">
            <?= Loc::getMessage('THEDIANKINA_NEWS_FILTER_FORM_SUBMIT_BUTTON_LABEL') ?>
        </button>
        <button type="reset" class="btn btn-secondary">
            <?= Loc::getMessage('THEDIANKINA_NEWS_FILTER_FORM_RESET_BUTTON_LABEL') ?>
        </button>
    </div>
</form>

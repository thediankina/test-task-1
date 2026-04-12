<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Main\UI\PageNavigation;

Loc::loadMessages(__FILE__);

class NewsComponent extends CBitrixComponent
{
    /**
     * {@inheritDoc}
     */
    public function onPrepareComponentParams($arParams): array
    {
        $arParams['IBLOCK_TYPE'] ??= '';
        $arParams['IBLOCK_ID'] ??= 0;

        return $arParams;
    }

    /**
     * {@inheritDoc}
     */
    public function executeComponent()
    {
        Loader::includeModule('iblock');

        try {
            $this->initResult();
        } catch (SystemException) {
            ShowError(Loc::getMessage('THEDIANKINA_NEWS_SOMETHING_WENT_WRONG_ERROR'));
        }

        $this->includeComponentTemplate();
    }

    /**
     * @return void
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    private function initResult(): void
    {
        $iblockId = (int)$this->arParams['IBLOCK_ID'];

        if (empty($iblockId)) {
            return;
        }

        $iblock = \Bitrix\Iblock\Iblock::wakeUp($iblockId);
        $elementTableClass = $iblock->getEntityDataClass();

        if (empty($elementTableClass)) {
            return;
        }

        $select = [
            'ID',
            'NAME',
            'ACTIVE_FROM',
            'CREATED_BY_USER',
            'PREVIEW_TEXT',
            'PREVIEW_PICTURE',
            'SECTION_' => 'IBLOCK_SECTION',
        ];
        $filter = [
            'ACTIVE' => 'Y',
        ];
        $order = [
            'ACTIVE_FROM' => 'DESC',
        ];

        $pagination = new PageNavigation('nav-news');
        $pagination->initFromUri();

        $pageSize = (int)$this->arParams['PAGE_SIZE'];

        if ($pageSize > 0) {
            $pagination->setPageSize($pageSize);
        }

        $totalCount = $elementTableClass::getCount($filter);
        $pagination->setRecordCount($totalCount);

        $elements = $elementTableClass::query()
            ->setSelect($select)
            ->setFilter($filter)
            ->setOrder($order)
            ->setLimit($pagination->getLimit())
            ->setOffset($pagination->getOffset())
            ->fetchCollection();

        /** @var \Bitrix\Iblock\EO_Element $element */
        foreach ($elements as $element) {
            $this->arResult['ITEMS'][] = [
                'ID' => $element->getId(),
                'NAME' => $element->getName(),
                'DATE' => $element->getActiveFrom()?->format('d.m.Y'),
                'AUTHOR' => $element->getCreatedByUser()?->getName(),
                'PREVIEW_TEXT' => $element->getPreviewText(),
                'PREVIEW_PICTURE_PATH' => \CFile::GetPath($element->getPreviewPicture()),
                'SECTION' => $element->getIblockSection()?->getName(),
            ];
        }

        if (!empty($this->arResult['ITEMS'])) {
            $this->arResult['NAV_OBJECT'] = $pagination;
        }
    }
}

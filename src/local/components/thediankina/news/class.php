<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Iblock\SectionTable;
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
        $this->prepareFilter();
        $this->prepareSections();

        $pagination = new PageNavigation('nav-news');
        $pagination->initFromUri();

        $pageSize = (int)$this->arParams['PAGE_SIZE'];

        if ($pageSize > 0) {
            $pagination->setPageSize($pageSize);
        }

        $this->prepareItems($pagination);

        if (!empty($this->arResult['ITEMS'])) {
            $this->arResult['NAV_OBJECT'] = $pagination;
        }
    }

    /**
     * @param PageNavigation $pagination
     *
     * @return void
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    private function prepareItems(PageNavigation $pagination): void
    {
        $this->arResult['ITEMS'] = [];

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
        $filter = array_filter([
            'ACTIVE' => 'Y',
            '%NAME' => $this->arResult['FILTER']['NAME'] ?: null,
            '>=ACTIVE_FROM' => $this->arResult['FILTER']['DATE_FROM'] ?: null,
            '<=ACTIVE_FROM' => $this->arResult['FILTER']['DATE_TO'] ?: null,
            '=IBLOCK_SECTION_ID' => $this->arResult['FILTER']['SECTION_ID'] ?: null,
        ]);
        $order = [
            'ACTIVE_FROM' => 'DESC',
        ];

        $totalCount = $elementTableClass::getCount($filter);
        $pagination->setRecordCount($totalCount);

        if ($totalCount === 0) {
            return;
        }

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
                'SECTION_ID' => $element->getIblockSection()?->getId(),
                'SECTION_NAME' => $element->getIblockSection()?->getName(),
            ];
        }
    }

    /**
     * @return void
     */
    private function prepareFilter(): void
    {
        $this->arResult['FILTER'] = [];

        $name = $this->request->get('name');
        $dateFrom = $this->request->get('dateFrom');
        $dateTo = $this->request->get('dateTo');
        $sectionId = $this->request->get('sectionId');

        if (!empty($name) && !is_array($name)) {
            $this->arResult['FILTER']['NAME'] = htmlspecialcharsbx(strip_tags($name));
        }

        if (!empty($dateFrom) && !is_array($dateFrom)) {
            $this->arResult['FILTER']['DATE_FROM'] = date('d.m.Y', (int)strtotime($dateFrom));
        }

        if (!empty($dateTo) && !is_array($dateTo)) {
            $this->arResult['FILTER']['DATE_TO'] = date('d.m.Y', (int)strtotime($dateTo));
        }

        if (!empty($sectionId) && !is_array($sectionId)) {
            $this->arResult['FILTER']['SECTION_ID'] = (int)$sectionId;
        }
    }

    /**
     * @return void
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    private function prepareSections(): void
    {
        $this->arResult['SECTIONS'] = [];

        $iblockId = (int)$this->arParams['IBLOCK_ID'];

        if (empty($iblockId)) {
            return;
        }

        $select = [
            'ID',
            'NAME',
        ];
        $filter = [
            'GLOBAL_ACTIVE' => 'Y',
            'IBLOCK_ID' => $iblockId,
        ];
        $order = [
            'SORT' => 'ASC',
        ];

        $this->arResult['SECTIONS'] = SectionTable::query()
            ->setSelect($select)
            ->setFilter($filter)
            ->setOrder($order)
            ->fetchAll();
    }
}

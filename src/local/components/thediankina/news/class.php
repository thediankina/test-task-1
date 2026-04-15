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

        $name = $this->request->get('name');

        if (!empty($name) && !is_array($name)) {
            $name = htmlspecialcharsbx(strip_tags($name));
            $filter['%NAME'] = $name;
            $this->arResult['FILTER']['NAME'] = $name;
        }

        $dateFrom = $this->request->get('dateFrom');
        $dateTo = $this->request->get('dateTo');

        if (!empty($dateFrom) && !is_array($dateFrom)) {
            $dateFrom = date('d.m.Y', (int)strtotime($dateFrom));
            $filter['>=ACTIVE_FROM'] = $dateFrom;
            $this->arResult['FILTER']['DATE_FROM'] = $dateFrom;
        }

        if (!empty($dateTo) && !is_array($dateTo)) {
            $dateTo = date('d.m.Y', (int)strtotime($dateTo));
            $filter['<=ACTIVE_FROM'] = $dateTo;
            $this->arResult['FILTER']['DATE_TO'] = $dateTo;
        }

        $sectionId = $this->request->get('sectionId');

        if (!empty($sectionId) && !is_array($sectionId)) {
            $sectionId = (int)$sectionId;
            $filter['=IBLOCK_SECTION_ID'] = $sectionId;
            $this->arResult['FILTER']['SECTION_ID'] = $sectionId;
        }

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
                'SECTION_ID' => $element->getIblockSection()?->getId(),
                'SECTION_NAME' => $element->getIblockSection()?->getName(),
            ];
        }

        if (!empty($this->arResult['ITEMS'])) {
            $this->arResult['NAV_OBJECT'] = $pagination;
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

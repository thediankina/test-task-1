<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Data\Cache;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Main\UI\PageNavigation;
use lib\exceptions\CustomException;

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

        $iblockId = (int)$this->arParams['IBLOCK_ID'];

        if (empty($iblockId)) {
            ShowError(Loc::getMessage('THEDIANKINA_NEWS_IBLOCK_NOT_FOUND_ERROR'));

            return;
        }

        $this->prepareFilter();

        $this->arResult['NAV_OBJECT'] = new PageNavigation('nav-news');
        $this->arResult['NAV_OBJECT']->initFromUri();

        $pageSize = (int)$this->arParams['PAGE_SIZE'];

        if ($pageSize > 0) {
            $this->arResult['NAV_OBJECT']->setPageSize($pageSize);
        }

        $cache = Cache::createInstance();
        $cacheId = 'thediankina_news_' . serialize($this->arResult['FILTER']) . '_' . $this->arResult['NAV_OBJECT']->getCurrentPage();
        $cacheDir = '/thediankina_news/' . $iblockId . '/';
        $cacheTtl = 3600;

        $taggedCache = Application::getInstance()->getTaggedCache();
        $tag = TAGGED_CACHE_THEDIANKINA_IBLOCK_TAG_PREFIX . '_' .$iblockId;

        if ($cache->initCache($cacheTtl, $cacheId, $cacheDir)) {
            $this->arResult = $cache->getVars();
        } else {
            $cache->startDataCache();

            try {
                $this->prepareItems();

                $taggedCache->startTagCache($cacheDir);
                $taggedCache->registerTag($tag);
                $taggedCache->endTagCache();
            } catch (CustomException $e) {
                $cache->abortDataCache();
                ShowError($e->getMessage());

                return;
            } catch (Exception $e) {
                $cache->abortDataCache();
                ShowError(Loc::getMessage('THEDIANKINA_NEWS_SOMETHING_WENT_WRONG_ERROR'));

                return;
            }

            $cache->endDataCache($this->arResult);
        }

        $this->includeComponentTemplate();
    }

    /**
     * @return void
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws CustomException
     */
    private function prepareItems(): void
    {
        $iblockId = (int)$this->arParams['IBLOCK_ID'];

        if (empty($iblockId)) {
            throw new CustomException(Loc::getMessage('THEDIANKINA_NEWS_IBLOCK_NOT_FOUND_ERROR'));
        }

        $iblock = \Bitrix\Iblock\Iblock::wakeUp($iblockId);
        $elementTableClass = $iblock->getEntityDataClass();

        if (empty($elementTableClass)) {
            throw new CustomException(Loc::getMessage('THEDIANKINA_NEWS_IBLOCK_NOT_FOUND_ERROR'));
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
        $this->arResult['NAV_OBJECT']->setRecordCount($totalCount);

        if ($totalCount === 0) {
            return;
        }

        $elements = $elementTableClass::query()
            ->setSelect($select)
            ->setFilter($filter)
            ->setOrder($order)
            ->setLimit($this->arResult['NAV_OBJECT']->getLimit())
            ->setOffset($this->arResult['NAV_OBJECT']->getOffset())
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
        $name = $this->request->get('name');
        $dateFrom = $this->request->get('dateFrom');
        $dateTo = $this->request->get('dateTo');
        $sectionId = $this->request->get('sectionId');

        if (!empty($name) && !is_array($name)) {
            $this->arResult['FILTER']['NAME'] = htmlspecialcharsbx(trim($name));
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
}

<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Iblock\SectionTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Data\Cache;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use lib\exceptions\CustomException;

Loc::loadMessages(__FILE__);

class NewsFilterComponent extends CBitrixComponent
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
        $iblockId = (int)$this->arParams['IBLOCK_ID'];

        if (empty($iblockId)) {
            ShowError(Loc::getMessage('THEDIANKINA_NEWS_FILTER_IBLOCK_NOT_FOUND_ERROR'));

            return;
        }

        $cache = Cache::createInstance();
        $cacheId = 'thediankina_news_filter_' . $iblockId;
        $cacheDir = '/thediankina_news_filter/';
        $cacheTtl = 3600;

        if ($cache->initCache($cacheTtl, $cacheId, $cacheDir)) {
            $this->arResult = $cache->getVars();
        } else {
            $cache->startDataCache();

            try {
                $this->initResult();
            } catch (CustomException $e) {
                $cache->abortDataCache();
                ShowError($e->getMessage());

                return;
            } catch (Exception $e) {
                $cache->abortDataCache();
                ShowError(Loc::getMessage('THEDIANKINA_NEWS_FILTER_SOMETHING_WENT_WRONG_ERROR'));

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
     * @throws LoaderException
     * @throws CustomException
     */
    private function initResult(): void
    {
        $this->prepareSections();
    }

    /**
     * @return void
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws LoaderException
     * @throws CustomException
     */
    private function prepareSections(): void
    {
        $iblockId = (int)$this->arParams['IBLOCK_ID'];

        if (empty($iblockId)) {
            throw new CustomException(Loc::getMessage('THEDIANKINA_NEWS_FILTER_IBLOCK_NOT_FOUND_ERROR'));
        }

        Loader::includeModule('iblock');

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

<?php

namespace lib\helpers;

use Bitrix\Iblock\IblockTable;
use Bitrix\Iblock\TypeLanguageTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;

class IblockHelper
{
    /**
     * @return array
     * @throws ArgumentException
     * @throws LoaderException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getTypes(): array
    {
        Loader::includeModule('iblock');

        $typeLangResult = TypeLanguageTable::getList([
            'select' => ['IBLOCK_TYPE_ID', 'NAME'],
            'filter' => ['LANGUAGE_ID' => 'ru'],
            'cache' => [
                'ttl' => 3600,
                'cache_joins' => true,
            ],
        ]);

        $types = [];
        while ($type = $typeLangResult->fetchObject()) {
            $types[$type->getIblockTypeId()] = $type->getName();
        }

        return $types;
    }

    /**
     * @param string $typeId
     *
     * @return array
     * @throws ArgumentException
     * @throws LoaderException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getIblocksByTypeId(string $typeId): array
    {
        Loader::includeModule('iblock');

        $iblockResult = IblockTable::getList([
            'select' => ['ID', 'NAME'],
            'filter' => [
                'IBLOCK_TYPE_ID' => $typeId,
                'ACTIVE' => 'Y',
            ],
            'order' => ['SORT' => 'ASC'],
            'cache' => [
                'ttl' => 3600,
                'cache_joins' => true,
            ],
        ]);

        $iblocks = [];
        while ($iblock = $iblockResult->fetchObject()) {
            $iblocks[$iblock->getId()] = $iblock->getName();
        }

        return $iblocks;
    }
}

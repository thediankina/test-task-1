<?php

namespace lib\helpers;

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;

class EventHandler
{
    /**
     * @param array|null $arFields
     *
     * @return void
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function clearIblockCache(?array $arFields): void
    {
        $iblockId = (int)$arFields['IBLOCK_ID'];

        if (!empty($iblockId)) {
            $taggedCache = Application::getInstance()->getTaggedCache();
            $tag = TAGGED_CACHE_THEDIANKINA_IBLOCK_TAG_PREFIX . '_' . $iblockId;
            $taggedCache->clearByTag($tag);
        }
    }
}

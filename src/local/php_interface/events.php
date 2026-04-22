<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\EventManager;
use lib\helpers\EventHandler;

$eventManager = EventManager::getInstance();
$eventManager->addEventHandler('iblock', 'OnAfterIBlockElementAdd', [EventHandler::class, 'clearIblockCache']);
$eventManager->addEventHandler('iblock', 'OnAfterIBlockElementUpdate', [EventHandler::class, 'clearIblockCache']);
$eventManager->addEventHandler('iblock', 'OnAfterIBlockElementDelete', [EventHandler::class, 'clearIblockCache']);

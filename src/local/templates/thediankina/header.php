<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @var CMain $APPLICATION
 */

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <?php $APPLICATION->ShowHead() ?>
    <title><?php $APPLICATION->ShowTitle() ?></title>
</head>
<body>
<div id="page-wrapper">
    <div id="panel"><?php $APPLICATION->ShowPanel() ?></div>

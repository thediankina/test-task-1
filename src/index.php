<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

/**
 * @var CMain $APPLICATION
 */

$APPLICATION->SetTitle("Тестовое задание");
?>
<?php
$APPLICATION->IncludeComponent(
    "thediankina:news",
    "",
    [
        "IBLOCK_TYPE" => "news",
        "IBLOCK_ID" => 1,
    ]
);
?>
<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>

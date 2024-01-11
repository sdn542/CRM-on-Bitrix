<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Web\Json;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\IOFactory;
CModule::IncludeModule("iblock");

function sanitizeInput($input)
{
	return htmlspecialcharsbx($input);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$clientId = sanitizeInput($_POST["client"]);
	$arFilter = array(
		"IBLOCK_CODE" => "contracts",
		"IBLOCK_TYPE" => "content",
	);
	if (!empty($clientId)) {
		$arFilter["PROPERTY_CLIENT"] = $clientId;
	}
	$arContracts = array();
	$arSelect = array("ID", "NAME", "PROPERTY_NUMBER");
	$rsElements = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
	while ($arElement = $rsElements->Fetch())
	{
		$arContracts[][$arElement["ID"]] = $arElement["PROPERTY_NUMBER_VALUE"];
	}
	$response = array(
		"success" => true,
		"message" => "Успешное получение списка договоров",
		"data" => $arContracts
	);
} else {
	$response = array(
		"success" => false,
		"message" => "Недопустимый метод запроса",
	);
}

echo Json::encode($response, JSON_UNESCAPED_UNICODE);

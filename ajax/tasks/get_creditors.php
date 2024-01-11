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
	$contractId = sanitizeInput($_POST["contract"]);
	$arFilter = array(
		"IBLOCK_CODE" => "creditors",
		"IBLOCK_TYPE" => "content",
	);
	if (!empty($contractId)) {
		$resContract = CIBlockElement::GetByID($contractId)->GetNextElement();
		$arContract = array(
			"FIELDS" => $resContract->GetFields(),
			"PROPERTIES" => $resContract->GetProperties()
		);
		$arFilter["ID"] = $arContract["PROPERTIES"]["CREDITOR"]["VALUE"];
	}
	$arCreditors = array();
	$arSelect = array("ID", "NAME", "PROPERTY_ADDRESS");
	$rsElements = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
	while ($arElement = $rsElements->Fetch())
	{
		$arCreditors[][$arElement["ID"]] = array("name" => $arElement["NAME"], "address" => $arElement["PROPERTY_ADDRESS_VALUE"]);
	}
	$response = array(
		"success" => true,
		"message" => "Успешное получение списка кредиторов",
		"data" => $arCreditors
	);
} else {
	$response = array(
		"success" => false,
		"message" => "Недопустимый метод запроса",
	);
}

echo Json::encode($response, JSON_UNESCAPED_UNICODE);

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
	$contractId = sanitizeInput($_POST["contract_id"]);
	$creditorId = sanitizeInput($_POST["creditor_id"]);

	$arFilterCreditor = array(
		"IBLOCK_CODE" => "contract_creditors",
		"IBLOCK_TYPE" => "content",
		"PROPERTY_CREDITOR" => $creditorId,
		"PROPERTY_CONTRACT" => $contractId,
	);
	$arSelectCreditor = array("ID", "NAME", "PROPERTY_NUMBER", "PROPERTY_DATE");
	$rsElementsCreditor = CIBlockElement::GetList(array(), $arFilterCreditor, false, false, $arSelectCreditor);
	if ($arElementCreditor = $rsElementsCreditor->Fetch()) {
		CIBlockElement::Delete($arElementCreditor["ID"]);
	}

	$iblockContract = CIBlock::GetList([], ['TYPE' => 'content', 'SITE_ID' => SITE_ID, "CODE" => 'contracts'], true)->Fetch();
	$resContract = CIBlockElement::GetByID($contractId)->GetNextElement();
	$arContract = array(
		"FIELDS" => $resContract->GetFields(),
		"PROPERTIES" => $resContract->GetProperties()
	);
	$arContract["PROPERTIES"]["CREDITOR"]["VALUE"] = array_diff($arContract["PROPERTIES"]["CREDITOR"]["VALUE"], array($creditorId));
	$propertyValues = array(
		"CREDITOR" => $arContract["PROPERTIES"]["CREDITOR"]["VALUE"]
	);
	CIBlockElement::SetPropertyValuesEx($contractId, $iblockContract["ID"], $propertyValues);
	$response = array(
		"success" => true,
		"message" => "Успешное удаление кредитора"
	);
} else {
	$response = array(
		"success" => false,
		"message" => "Недопустимый метод запроса",
	);
}

// echo Json::encode($response, JSON_UNESCAPED_UNICODE);
?>
<script>
	history.back()
</script>

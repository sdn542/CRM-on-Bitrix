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
	$iblock = CIBlock::GetList([], ['TYPE' => 'content', 'SITE_ID' => SITE_ID, "CODE" => 'contract_creditors'], true)->Fetch();
	$iblockId = $iblock['ID'];
	$el = new CIBlockElement;
	$arCreditor = array(
		"IBLOCK_ID" => $iblockId,
		"NAME" => "Кредитор",
		"ACTIVE" => "Y",
		"PROPERTY_VALUES" => array(
			"CREDITOR" => sanitizeInput($_POST["creditor"]),
			"CONTRACT" => $contractId,
			"NUMBER" => sanitizeInput($_POST["number"]),
			"DATE" => sanitizeInput($_POST["date"])
		)
	);

	if ($creditorID = $el->Add($arCreditor)) {
		$iblockContract = CIBlock::GetList([], ['TYPE' => 'content', 'SITE_ID' => SITE_ID, "CODE" => 'contracts'], true)->Fetch();
		$resContract = CIBlockElement::GetByID($contractId)->GetNextElement();
		$arContract = array(
			"FIELDS" => $resContract->GetFields(),
			"PROPERTIES" => $resContract->GetProperties()
		);
		$arContract["PROPERTIES"]["CREDITOR"]["VALUE"][] = sanitizeInput($_POST["creditor"]);
		$propertyValues = array(
			"CREDITOR" => $arContract["PROPERTIES"]["CREDITOR"]["VALUE"]
		);
		CIBlockElement::SetPropertyValuesEx($contractId, $iblockContract["ID"], $propertyValues);
		// Элемент успешно создан
		$response = array(
			"success" => true,
			"message" => "Кредитор успешно добавлен",
		);
	} else {
		// Ошибка создания элемента
		$response = array(
			"success" => false,
			"message" => "Ошибка при добавлении кредитора",
		);
	}
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

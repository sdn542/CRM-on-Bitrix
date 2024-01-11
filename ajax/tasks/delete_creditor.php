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
	$creditorsIds = $_POST["creditor_id"];
	foreach ($creditorsIds as $creditor)
	{
		$resAddressed = CIBlockElement::GetByID($creditor)->GetNextElement();
		$arAddressed = array(
			"PROPERTIES" => $resAddressed->GetProperties()
		);
		$resCreditor = CIBlockElement::GetByID($arAddressed["PROPERTIES"]["CREDITOR"]["VALUE"])->GetNextElement();
		$arCreditor = array(
			"FIELDS" => $resCreditor->GetFields()
		);
		$iblockHistory = CIBlock::GetList([], ['TYPE' => 'content', 'SITE_ID' => SITE_ID, "CODE" => 'history'], true)->Fetch();
		$iblockHistoryId = $iblockHistory['ID'];
		$elHistory = new CIBlockElement;
		$arHistory = array(
			"IBLOCK_ID" => $iblockHistoryId,
			"NAME" => "Изменение задачи " . $_POST["task_id"],
			"ACTIVE" => "Y",
			"ACTIVE_FROM" => FormatDate("d.m.Y", time()),
			"PROPERTY_VALUES" => array(
				"DATETIME" => FormatDate("d.m.Y H:m:i", time()),
				"USER" => $USER->GetID(),
				"TASK" => $_POST["task_id"],
				"CHANGED" => "Адресат",
				"BEFORE" => $arCreditor["FIELDS"]["NAME"],
				"AFTER" => "Удалён"
			)
		);
		$elHistory->Add($arHistory);
		CIBlockElement::Delete($creditor);
	}
	$response = array(
		"success" => true,
		"message" => "Успешное удаление адресата"
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
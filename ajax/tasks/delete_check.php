<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Web\Json;
use Bitrix\Iblock\IblockTable;

CModule::includeModule('iblock');
CModule::IncludeModule("main");

function sanitizeInput($input)
{
	return htmlspecialcharsbx($input);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (!empty(sanitizeInput($_POST["file_id"])) && !empty(sanitizeInput($_POST["task_id"])) && !empty(sanitizeInput($_POST["property_code"]))) {
		$iblock = IblockTable::getList([
			'select' => ['ID'],
			'filter' => ['CODE' => "tasks"],
		])->fetch();
		if ($iblock) {
			$VALUES = array();
			$dbElement = CIBlockElement::GetProperty($iblock["ID"], sanitizeInput($_POST["task_id"]), array("sort" => "asc"), Array("CODE"=>sanitizeInput($_POST["property_code"])));
			while ($arElement = $dbElement->GetNext())
			{
				$value = ($arElement["VALUE"] != sanitizeInput($_POST["file_id"])) ? array("VALUE" => CFile::GetFileArray($arElement["VALUE"]), "DESCRIPTION" => $arElement["DESCRIPTION"]) : array("del" => "Y");
				$VALUES[sanitizeInput($_POST["property_code"])][$arElement["PROPERTY_VALUE_ID"]] = $value;
			}
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
					"CHANGED" => $_POST["property_code"] == "TEMPLATE" ? "Шаблон" : "Чек",
					"BEFORE" => CFile::GetFileArray($_POST["file_id"])["ORIGINAL_NAME"],
					"AFTER" => "Удалён"
				)
			);
			$elHistory->Add($arHistory);
			CIBlockElement::SetPropertyValuesEx(sanitizeInput($_POST["task_id"]), $iblock["ID"], $VALUES);
			$response = array(
				"success" => true,
				"message" => "Файл успешно удален",
			);
			CFile::Delete(sanitizeInput($_POST["file_id"]));
		} else {
			$response = array(
				"success" => false,
				"message" => "Ошибка инфоблок не найден",
			);
		}
	} else {
		$response = array(
			"success" => false,
			"message" => "Не переданы обязательные данные",
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


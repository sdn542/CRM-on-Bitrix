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
	if(!empty(sanitizeInput($_POST["doc_id"])) && !empty(sanitizeInput($_POST["property_code"])) && !empty($_POST['file_id'])) {
		$iblock = IblockTable::getList([
			'select' => ['ID'],
			'filter' => ['CODE' => "incoming-documents"],
		])->fetch();
		if ($iblock) {
			$VALUES = array();
			$VALUES[sanitizeInput($_POST["property_code"])] = array("del" => "Y");
			CIBlockElement::SetPropertyValuesEx(sanitizeInput($_POST["doc_id"]), $iblock["ID"], $VALUES);
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

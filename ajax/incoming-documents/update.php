<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Web\Json;
use Bitrix\Iblock\IblockTable;

CModule::IncludeModule("main");
CModule::IncludeModule('iblock');

function sanitizeInput($input)
{
	return htmlspecialcharsbx($input);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$docId = sanitizeInput($_POST["doc_id"]);
	$iblock = IblockTable::getList([
		'select' => ['ID'],
		'filter' => ['CODE' => "incoming-documents"],
	])->fetch();
	if ($iblock && $docId) {
		$VALUES = array();
		foreach ($_POST as $key => $value){
			$VALUES[$key] = sanitizeInput($value);
		}

		CIBlockElement::SetPropertyValuesEx($docId, $iblock["ID"], $VALUES);
		$response = array(
			"success" => true,
			"message" => "Свойство обновлено",
		);
	} else {
		$response = array(
			"success" => false,
			"message" => "Инфоблок не найден",
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

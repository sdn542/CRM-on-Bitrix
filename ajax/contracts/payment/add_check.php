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
	if(!empty(sanitizeInput($_POST["payment_id"])) && !empty(sanitizeInput($_POST["property_code"])) && !empty($_FILES['files'])) {
		$iblock = IblockTable::getList([
			'select' => ['ID'],
			'filter' => ['CODE' => "payments"],
		])->fetch();
		if ($iblock) {
			$VALUES = array();
			$dbElement = CIBlockElement::GetProperty($iblock["ID"], sanitizeInput($_POST["payment_id"]), array("sort" => "asc"), Array("CODE"=>sanitizeInput($_POST["property_code"])));
			while ($arElement = $dbElement->GetNext())
			{
				$VALUES[sanitizeInput($_POST["property_code"])][$arElement["PROPERTY_VALUE_ID"]] = array("VALUE" => CFile::GetFileArray($arElement["VALUE"]), "DESCRIPTION" => $arElement["DESCRIPTION"]);
			}
			$requestFiles = $_FILES['files'];
			foreach ($requestFiles["name"] as $index => $requestFileName) {
				$arFile = array(
					"name" => $requestFileName,
					"full_path" => $requestFiles["full_path"][$index],
					"type" => $requestFiles["type"][$index],
					"tmp_name" => $requestFiles["tmp_name"][$index],
					"error" => $requestFiles["error"][$index],
					"size" => $requestFiles["size"][$index]
				);
				$VALUES[sanitizeInput($_POST["property_code"])][] = array(
					"VALUE" => $arFile,
					"DESCRIPTION" => date('d.m.Y')
				);
			}
			CIBlockElement::SetPropertyValuesEx(sanitizeInput($_POST["payment_id"]), $iblock["ID"], $VALUES);
			$response = array(
				"success" => true,
				"message" => "Файлы успешно загружены",
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

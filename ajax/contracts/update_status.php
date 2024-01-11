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
	$iblock = IblockTable::getList([
		'select' => ['ID'],
		'filter' => ['CODE' => "contracts"],
	])->fetch();
	if ($iblock) {
		$VALUES = array(
			"STATUS" => sanitizeInput($_POST["status"]),
			"STATUS_CHECK" => sanitizeInput($_POST["status_check"])
		);
		$property_enums = CIBlockPropertyEnum::GetList(Array("ID"=>"ASC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$iblock["ID"], "CODE" => "STATUS_CHECK", "ID" => sanitizeInput($_POST["status_check"])));
		if($enum_fields = $property_enums->GetNext())
		{
			if ($enum_fields["XML_ID"] == "APPROVED")
			{
				$propertyEnumsStatus = CIBlockPropertyEnum::GetList(Array("ID"=>"ASC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$iblock["ID"], "CODE" => "STATUS", "XML_ID" => "INWORK"));
				if($enumFieldsStatus = $propertyEnumsStatus->GetNext())
				{
					$VALUES["STATUS"] = $enumFieldsStatus["ID"];
				}
			}
		}
		CIBlockElement::SetPropertyValuesEx(sanitizeInput($_POST["contract_id"]), $iblock["ID"], $VALUES);
		$response = array(
			"success" => true,
			"message" => "Статус обновлен",
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

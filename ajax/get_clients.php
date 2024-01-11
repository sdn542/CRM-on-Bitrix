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
	$fio = sanitizeInput($_REQUEST["fio"]);
	$groupCode = "clients";
	$rsGroup = CGroup::GetList($by = "c_sort", $order = "asc", array("STRING_ID" => $groupCode));
	$arGroup = $rsGroup->Fetch();
	$groupId = $arGroup["ID"];
	if ($groupId) {
		$userFilter = array(
			"GROUPS_ID" => array($groupId)
		);
		if (!empty($fio)) {
			$nameParts = explode(" ", $fio);
			$lastName = isset($nameParts[0]) ? $nameParts[0] : "";
			$name = isset($nameParts[1]) ? $nameParts[1] : "";
			$secondName = isset($nameParts[2]) ? $nameParts[2] : "";
			$userFilter["NAME"] = "$lastName & $name & $secondName";
		}
		$userParams = array("SELECT" => array("ID", "NAME"));

		$rsUsers = CUser::GetList($by = "ID", $order = "asc", $userFilter, $userParams);
		$arrUsers = array();
		while ($arUser = $rsUsers->Fetch()) {
			$arrUsers[$arUser["ID"]] = $arUser["LAST_NAME"] . " " . $arUser["NAME"] . (!empty($arUser["SECOND_NAME"]) ? " " . $arUser["SECOND_NAME"] : "");
		}
	}
	$response = array(
		"success" => true,
		"message" => "Успешное получение списка пользователей",
		"data" => $arrUsers,
	);
} else {
	$response = array(
		"success" => false,
		"message" => "Недопустимый метод запроса",
	);
}

echo Json::encode($response, JSON_UNESCAPED_UNICODE);

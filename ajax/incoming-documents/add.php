<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Web\Json;

CModule::IncludeModule("main");
CModule::IncludeModule('iblock');

function sanitizeInput($input)
{
	return htmlspecialcharsbx($input);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$iblock = CIBlock::GetList([], ['TYPE' => 'content', 'SITE_ID' => SITE_ID, "CODE" => 'incoming-documents'], true)->Fetch();
		if ($iblock) {
			$iblockId = $iblock['ID'];
			$el = new CIBlockElement;

			$arDoc = array(
				"IBLOCK_ID" => $iblockId,
				"NAME" => $_POST["NAME"],
				"ACTIVE" => "Y",
				"ACTIVE_FROM" => FormatDate("d.m.Y", time()),
				"PROPERTY_VALUES" => $_POST["PROPS"]
			);

			$arFile = array(
				"name" => $_FILES["PROPS"]["name"]["FILE"],
				"full_path" => $_FILES["PROPS"]["full_path"]["FILE"],
				"type" => $_FILES["PROPS"]["type"]["FILE"],
				"tmp_name" => $_FILES["PROPS"]["tmp_name"]["FILE"],
				"error" => $_FILES["PROPS"]["error"]["FILE"],
				"size" => $_FILES["PROPS"]["size"]["FILE"]
			);

			$arDoc["PROPERTY_VALUES"]["FILE"] = $arFile;

			if ($docID = $el->Add($arDoc)) {
				// Элемент успешно создан
				$response = array(
					"success" => true,
					"message" => "Входящий документ успешно создан",
				);
			} else {
				// Ошибка создания элемента
				$response = array(
					"success" => false,
					"message" => "Ошибка при создании документа: " . $el->LAST_ERROR,
				);
			}
		} else {
			// Ошибка инфоблока
			$response = array(
				"success" => false,
				"message" => "Ошибка инфоблок не найден",
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


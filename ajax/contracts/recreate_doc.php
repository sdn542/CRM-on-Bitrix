<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Web\Json;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\IOFactory;
CModule::IncludeModule("iblock");

function sanitizeInput($input)
{
	return htmlspecialcharsbx($input);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$contractId = sanitizeInput($_POST["contract_id"]);
	if (isset($contractId)) {
		$resContract = CIBlockElement::GetByID($contractId)->GetNextElement();
		$arContract = array(
			"FIELDS" => $resContract->GetFields(),
			"PROPERTIES" => $resContract->GetProperties()
		);
		if(!empty($arContract["PROPERTIES"]["PAYMENT"]["VALUE"]))
		{
			$table = new Table();
			$table->addRow();
			$table->addCell()->addText("Дата");
			$table->addCell()->addText("Сумма платежа");
			$table->addCell()->addText("Статус оплаты услуги");
			$table->addCell()->addText("Почтовые расходы");
			$table->addCell()->addText("Статус оплаты почты");
			foreach ($arContract["PROPERTIES"]["PAYMENT"]["VALUE"] as $payment) {
				$resPayment = CIBlockElement::GetByID($payment)->GetNextElement();
				$arPayment = array(
					"FIELDS" => $resPayment->GetFields(),
					"PROPERTIES" => $resPayment->GetProperties()
				);
				$table->addRow();
				$table->addCell()->addText($arPayment["PROPERTIES"]["DATE"]["VALUE"]);
				$table->addCell()->addText($arPayment["PROPERTIES"]["SUM"]["VALUE"]);
				$table->addCell()->addText($arPayment["PROPERTIES"]["STATUS_SERVICE"]["VALUE"]);
				$table->addCell()->addText($arPayment["PROPERTIES"]["POSTAGE"]["VALUE"]);
				$table->addCell()->addText($arPayment["PROPERTIES"]["STATUS_POST"]["VALUE"]);
			}
		}
		$arUser = CUser::GetByID($arContract["PROPERTIES"]["LAWYER"]["VALUE"])->Fetch();
		$arClient = CUser::GetByID($arContract["PROPERTIES"]["CLIENT"]["VALUE"])->Fetch();
		$iblockTemplates = CIBlock::GetList([], ['TYPE' => 'content', 'SITE_ID' => SITE_ID, "CODE" => 'templates-docs'], true)->Fetch();
		$property_enums = CIBlockPropertyEnum::GetList(
			Array("ID"=>"ASC", "SORT"=>"ASC"),
			Array("IBLOCK_ID"=>$iblockTemplates["ID"], "CODE"=>"TYPE_CONTRACT", "XML_ID" => $arContract["PROPERTIES"]["TYPE"]["VALUE_XML_ID"])
		);
		if ($enum_fields = $property_enums->GetNext()) {
			$arSelect = Array("ID", "IBLOCK_ID");
			$arFilter = Array("IBLOCK_ID"=>$iblockTemplates["ID"], "PROPERTY_TYPE_CONTRACT" => $enum_fields["ID"]);
			$res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
			while($ob = $res->GetNextElement()){
				$arTemplate = array(
					"FIELDS" => $ob->GetFields(),
					"PROPERTIES" => $ob->GetProperties()
				);
			}
		}

		$phpWord = new TemplateProcessor($_SERVER["DOCUMENT_ROOT"] . CFile::GetPath($arTemplate["PROPERTIES"]["TEMPLATE_DOC"]["VALUE"]));
		$arVariables = array(
			"\${number}" => $arContract["PROPERTIES"]["NUMBER"]["VALUE"],
			"\${lawyer}" => $arUser["LAST_NAME"] . " " . $arUser["NAME"] . (!empty($arUser["SECOND_NAME"]) ? " " . $arUser["SECOND_NAME"] : ""),
			"\${client}" => $arClient["LAST_NAME"] . " " . $arClient["NAME"] . (!empty($arClient["SECOND_NAME"]) ? " " . $arClient["SECOND_NAME"] : ""),
			"\${date}" => $arContract["ACTIVE_FROM"],
			"\${amount}" => $arContract["PROPERTIES"]["CONTRACT_AMOUNT"]["VALUE"],
			"\${address}" => $arClient["UF_RESIDENCE_ADDRESS"],
			"\${passportserires}" => $arClient["UF_SERIES"],
			"\${passportnumber}" => $arClient["UF_NUMBER"],
		);

		foreach ($arVariables as $variable => $value) {
			$phpWord->setValue($variable, $value);
		}

		$phpWord->setComplexBlock("\${schedule}", $table);

		$tempFilePath = tempnam(sys_get_temp_dir(), 'contract_').'.docx';
		$phpWord->saveAs($tempFilePath);

		$arFile = CFile::MakeFileArray($tempFilePath);
		$arFile["MODULE_ID"] = "iblock";
		CIBlockElement::SetPropertyValueCode($contractId, "DOCS_BY_CLIENT", Array("VALUE"=>$arFile, "DESCRIPTION" => date('d.m.Y')));

		unlink($tempFilePath);

		exit();
	} else {
		$response = array(
			"success" => false,
			"message" => "Отсутствуют необходимые параметры",
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

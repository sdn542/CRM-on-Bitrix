<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Web\Json;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\IOFactory;

CModule::IncludeModule("main");

function sanitizeInput($input)
{
	return htmlspecialcharsbx($input);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$clientId = sanitizeInput($_POST["client_id"]);
	$newUser = new CUser;
	$arClient = array(
		"NAME" => sanitizeInput($_POST["first_name"]),
		"LAST_NAME" => sanitizeInput($_POST["last_name"]),
		"SECOND_NAME" => sanitizeInput($_POST["second_name"]),
		"EMAIL" => sanitizeInput($_POST["email"]),
		"LOGIN" => sanitizeInput($_POST["email"]),
		"GROUP_ID" => array(5),
		"PERSONAL_BIRTHDAY" => FormatDate("d.m.Y", MakeTimeStamp(sanitizeInput($_POST["date_birth"]))),
		"PERSONAL_PHONE" => sanitizeInput($_POST["phone"]),
		"UF_PLACE_BIRTH" => sanitizeInput($_POST["place_birth"]),
		"UF_EMPLOYMENT_INCOME" => sanitizeInput($_POST["employment_and_income"]),
		"UF_OWNERSHIP" => sanitizeInput($_POST["property_presence"]),
		"UF_THREE_YEAR_TRANSACTIONS" => sanitizeInput($_POST["transactions_three_year_period"]),
		"UF_CUSTOMER_EXPECTED_OUTCOME" => sanitizeInput($_POST["expected_client_result"]),
		"UF_SERIES" => sanitizeInput($_POST["series"]),
		"UF_DATE_ISSUED" => FormatDate("d.m.Y", MakeTimeStamp(sanitizeInput($_POST["date_issued"]))),
		"UF_ISSUING_AUTHORITY" => sanitizeInput($_POST["issued_by"]),
		"UF_INN" => sanitizeInput($_POST["inn"]),
		"UF_SNILS" => sanitizeInput($_POST["snils"]),
		"UF_RESIDENCE_ADDRESS" => sanitizeInput($_POST["residence_address"]),
		"UF_NUMBER" => sanitizeInput($_POST["number"]),
		"UF_COSIGNERS_COBORROWERS" => sanitizeInput($_POST["guarantors_cobligors"]),
		"UF_PREEXISTING_DELINQUENCIES" => sanitizeInput($_POST["delinquencies_at_contract"]),
		"UF_MFOS_DEBTS" => sanitizeInput($_POST["overdue_mfo"]),
		"UF_UTILITY_ARREARS" => sanitizeInput($_POST["overdue_housing_services"]),
		"UF_CREDITOR_COUNT" => sanitizeInput($_POST["creditors_number"]),
		"UF_JUDGMENT_DECREES" => sanitizeInput($_POST["judicial_acts"]),
		"UF_COLLATERAL_VALUE" => sanitizeInput($_POST["total_encumbrance_amount"]),
		"UF_EXECUTION_PROCEEDINGS" => sanitizeInput($_POST["enforcement_proceedings"]),
		"UF_TAX_OBLIGATIONS" => sanitizeInput($_POST["tax_liabilities"]),
		"UF_NOTES" => sanitizeInput($_POST["notes"]),
		"UF_CHANGE_HISTORY" => sanitizeInput($_POST["change_history"]),
		"UF_WORK_PLAN" => sanitizeInput($_POST["work_plan"]),
	);

	if($newUser->Update($clientId, $arClient)){
		$response = array(
			"success" => true,
			"message" => "Успешное обновление данных пользователя",
		);
	} else {
		// Ошибка создания пользователя
		$response = array(
			"success" => false,
			"message" => "Ошибка при создании пользователя: " . $newUser->LAST_ERROR,
		);
	}
} else {
	$response = array(
		"success" => false,
		"message" => "Недопустимый метод запроса",
	);
}

//echo Json::encode($response, JSON_UNESCAPED_UNICODE);
?>
<script>
    history.back()
</script>

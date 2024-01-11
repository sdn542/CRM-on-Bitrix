<?
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');
IncludeTemplateLangFile(__FILE__);
$APPLICATION->SetTitle('Создание нового клиента');
$APPLICATION->SetPageProperty("description",'Создание нового клиента');

use Bitrix\Main\Loader;
use Bitrix\Iblock\PropertyEnumerationTable;

$optionsTypeContract = array();
$iblock = CIBlock::GetList([], ['TYPE' => 'content', 'SITE_ID' => SITE_ID, "CODE" => 'contracts'], true)->Fetch();
if ($iblock) {
	$iblockId = $iblock['ID'];

	// Получение ID свойства по символьному коду и ID инфоблока
	$property = \CIBlockProperty::GetList([], ['CODE' => 'TYPE', 'IBLOCK_ID' => $iblockId])->Fetch();
	if ($property) {
		$propertyId = $property['ID'];

		// Получение списка значений свойства типа "список"
		$propertyEnum = PropertyEnumerationTable::getList([
			'filter' => ['PROPERTY_ID' => $propertyId],
			'select' => ['ID', 'VALUE']
		])->fetchAll();

		foreach ($propertyEnum as $enum) {
			$optionsTypeContract[$enum['ID']] = $enum['VALUE'];
		}
	}
}

$optionsCreditors = array();
$iblockCreditors = CIBlock::GetList([], ['TYPE' => 'content', 'SITE_ID' => SITE_ID, "CODE" => 'creditors'], true)->Fetch();
if ($iblockCreditors) {
	$iblockId = $iblockCreditors['ID'];

	$arSelect = array("ID", "NAME");
	$arFilter = array("IBLOCK_ID" => $iblockId);
	$res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);

	while ($ob = $res->GetNextElement()) {
		$arFields = $ob->GetFields();
		$optionsCreditors[$arFields['ID']] = $arFields['NAME'];
	}
}
?>
<main class="main">
	<div class="container">
		<h1 class="title"><?= GetMessage("TITLE_CREATE_NEW_CLIENT") ?></h1>
		<form method="post" action="/ajax/contracts/add.php">
			<div class="d-flex justify-content-between mb-4">
				<div class="create-client-tabs">
					<h3 data-step-tab="step-agreement"><?= GetMessage("TITLE_TAB_CONTRACT") ?></h3>
					<h3 data-step-tab="step-client"><?= GetMessage("TITLE_TAB_CLIENT") ?></h3>
					<h3 data-step-tab="step-payments"><?= GetMessage("TITLE_TAB_SUM_PAYMENT_SCHEDULE") ?></h3>
					<h3 data-step-tab="step-creditor"><?= GetMessage("TITLE_TAB_CREDITORS") ?></h3>
				</div>
				<div class="buttons">
					<button class="btn btn-primary form-button" type="submit">Сформировать договор</button>
					<a class="btn btn-secondary" href="/lawyer/">Отмена</a>
				</div>
			</div>
	
			<div class="block" data-step="step-agreement">
				<div class="row mb-3">
					<div class="col-5">
						<label class="form-label" for="type_contract">
							<?= GetMessage("TITLE_TAB_CONTRACT_INPUT_TYPE") ?><span>*</span>
						</label>
						<? if (!empty($optionsTypeContract)): ?>
							<select class="form-select" name="type_contract" id="type_contract" required>
								<? foreach ($optionsTypeContract as $key => $option): ?>
									<option value="<?= $key ?>"><?= $option ?></option>
								<? endforeach; ?>
							</select>
						<? endif; ?>
					</div>
					<div class="col-2">
						<label class="form-label" for="date_contract">
							<?= GetMessage("TITLE_TAB_CONTRACT_INPUT_DATE") ?>
						</label>
						<input class="form-control" type="date" name="date_contract" id="date_contract">
					</div>
				</div>	
				<div class="row mb-3">
					<div class="col-5">
						<label class="form-label" for="responsible">
							Ответственный<span>*</span>
						</label>
						<select class="form-select" name="responsible" id="responsible" required>
							<?
							$groupCode = "director|chief_lawyers|senior_lawyers|junior_lawyers";
							$rsGroup = CGroup::GetList($by = "c_sort", $order = "asc", array("STRING_ID" => $groupCode));
							$groupIds = array();
							while($arGroup = $rsGroup->Fetch()) {
								$groupIds[] = $arGroup["ID"];
							}
							if ($groupIds) {
								$userFilter = array("GROUPS_ID" => $groupIds);
								$userParams = array("SELECT" => array("ID", "NAME", "EMAIL"));

								$rsUsers = CUser::GetList($by = "ID", $order = "asc", $userFilter, $userParams);
								while ($arUser = $rsUsers->Fetch()) {?>
									<option value="<?= $arUser["ID"] ?>"><?= $arUser["LAST_NAME"] . " " . $arUser["NAME"] . (!empty($arUser["SECOND_NAME"]) ? " " . $arUser["SECOND_NAME"] : "") ?></option>
								<?}
							}
							?>
						</select>
					</div>
				</div>				
				<div class="row">
					<div class="col-5">
						<label class="form-label" for="contract_signer">
							Заключивший<span>*</span>
						</label>
						<select class="form-select" name="contract_signer" id="contract_signer" required>
							<?
						$groupCode = "director|chief_lawyers|senior_lawyers|junior_lawyers";
							$rsGroup = CGroup::GetList($by = "c_sort", $order = "asc", array("STRING_ID" => $groupCode));
							$groupIds = array();
							while($arGroup = $rsGroup->Fetch()) {
								$groupIds[] = $arGroup["ID"];
							}
							if ($groupIds) {
								$userFilter = array("GROUPS_ID" => $groupIds);
								$userParams = array("SELECT" => array("ID", "NAME", "EMAIL"));

								$rsUsers = CUser::GetList($by = "ID", $order = "asc", $userFilter, $userParams);
								while ($arUser = $rsUsers->Fetch()) {?>
								<option value="<?= $arUser["ID"] ?>" <?= $arUser["ID"] == $USER->GetID() ? "selected" : "" ?>><?= $arUser["LAST_NAME"] . " " . $arUser["NAME"] . (!empty($arUser["SECOND_NAME"]) ? " " . $arUser["SECOND_NAME"] : "") ?></option>
								<?}
							}
							?>
						</select>
					</div>
				</div>
				<button data-next-step="step-client" type="button" class="btn btn-primary my-2">Далее</button>
			</div>
	
			<div class="block" data-step="step-client">
				<div class="inputs-group">
					<h4 class="title-secondary"><?= GetMessage("TITLE_TAB_CLIENT_FORM_CLIENT_DATA") ?></h4>
					<div class="row">
						<div class="col-3">
							<label class="form-label" for="last_name">
								<?= GetMessage("TITLE_TAB_CLIENT_FORM_CLIENT_INPUT_LAST") ?><span>*</span>
							</label>
							<input class="form-control" type="text" name="last_name" id="last_name" required>
						</div>
						<div class="col-3">
							<label class="form-label" for="place_birth">
								<?= GetMessage("TITLE_TAB_CLIENT_FORM_CLIENT_INPUT_PLACE") ?><span>*</span>
							</label>
							<input class="form-control" type="text" name="place_birth" id="place_birth" required>
						</div>
						<div class="col-3">
							<label class="form-label" for="employment_and_income">
								<?= GetMessage("TITLE_TAB_CLIENT_FORM_CLIENT_INPUT_EMPLOYMENT_AND_INCOME") ?>
							</label>
							<input class="form-control" type="text" name="employment_and_income" id="employment_and_income">
						</div>
						<div class="col-3">
							<label class="form-label" for="expected_client_result">
									<?= GetMessage("TITLE_TAB_CLIENT_FORM_CLIENT_INPUT_EXPECTED_CLIENT_RESULT") ?>
								</label>
								<input class="form-control" type="text" name="expected_client_result" id="expected_client_result">
							</div>
					</div>
					<div class="row">
						<div class="col-3">
							<label class="form-label" for="first_name">
								<?= GetMessage("TITLE_TAB_CLIENT_FORM_CLIENT_INPUT_FIRST") ?><span>*</span>
							</label>
							<input class="form-control" type="text" name="first_name" id="first_name" required>
						</div>
						<div class="col-3">
							<label class="form-label" for="place_birth">
								<?= GetMessage("TITLE_TAB_CLIENT_FORM_CLIENT_INPUT_DATE") ?><span>*</span>
							</label>
							<input class="form-control" type="date" name="date_birth" id="date_birth" required>
						</div>
						<div class="col-3">
							<label class="form-label" for="property_presence">
								<?= GetMessage("TITLE_TAB_CLIENT_FORM_CLIENT_INPUT_PROPERTY_PRESENCE") ?>
							</label>
							<input class="form-control" type="text" name="property_presence" id="property_presence">
						</div>
					</div>
					<div class="row">
						<div class="col-3">
							<label class="form-label" for="second_name">
								<?= GetMessage("TITLE_TAB_CLIENT_FORM_CLIENT_INPUT_SECOND") ?>
							</label>
							<input class="form-control" type="text" name="second_name" id="second_name">
						</div>
						<div class="col-3">
							<label class="form-label" for="email">
								<?= GetMessage("TITLE_TAB_CLIENT_FORM_CLIENT_INPUT_EMAIL") ?><span>*</span>
							</label>
							<input class="form-control" type="email" name="email" id="email" required>
						</div>
						<div class="col-3">
							<label class="form-label" for="transactions_three_year_period">
								<?= GetMessage("TITLE_TAB_CLIENT_FORM_CLIENT_INPUT_TRANSACTIONS_THREE_YEAR_PERIOD") ?>
							</label>
							<input class="form-control" type="text" name="transactions_three_year_period" id="transactions_three_year_period">		
						</div>
					</div>
				</div>
				
				<div class="inputs-group">
					<h4 class="title-secondary"><?= GetMessage("TITLE_TAB_CLIENT_FORM_PASSPORT_DATA") ?></h4>
					<div class="row">
						<div class="col-3">
							<label class="form-label" for="series">
								<?= GetMessage("TITLE_TAB_CLIENT_FORM_PASSPORT_INPUT_SERIES") ?><span>*</span>
							</label>
							<input class="form-control" type="text" name="series" id="series" required>
						</div>
						<div class="col-3">
							<label class="form-label" for="date_issued">
								<?= GetMessage("TITLE_TAB_CLIENT_FORM_PASSPORT_INPUT_DATE_ISSUED") ?><span>*</span>
							</label>
							<input class="form-control" type="text" name="date_issued" id="date_issued" required>
						</div>
						<div class="col-3">
							<label class="form-label" for="issued_by">
								<?= GetMessage("TITLE_TAB_CLIENT_FORM_PASSPORT_INPUT_ISSUED_BY") ?><span>*</span>
							</label>
							<input class="form-control" type="text" name="issued_by" id="issued_by" required>
						</div>
						<div class="col-3">
							<label class="form-label" for="inn">
								<?= GetMessage("TITLE_TAB_CLIENT_FORM_PASSPORT_INPUT_INN") ?>
							</label>
							<input class="form-control" type="text" name="inn" id="inn" >
						</div>
					</div>
					<div class="row">
						<div class="col-3">
							<label class="form-label" for="snils">
								<?= GetMessage("TITLE_TAB_CLIENT_FORM_PASSPORT_INPUT_SNILS") ?>
							</label>
							<input class="form-control" type="text" name="snils" id="snils">
						</div>
						<div class="col-3">
							<label class="form-label" for="residence_address">
								<?= GetMessage("TITLE_TAB_CLIENT_FORM_PASSPORT_INPUT_RESIDENCE_ADDRESS") ?><span>*</span>
							</label>
							<input class="form-control" type="text" name="residence_address" id="residence_address" required>
						</div>
						<div class="col-3">
							<label class="form-label" for="number">
								<?= GetMessage("TITLE_TAB_CLIENT_FORM_PASSPORT_INPUT_NUMBER") ?><span>*</span>
							</label>
							<input class="form-control" type="text" name="number" id="number" required>
						</div>
						<div class="col-3">
							<label class="form-label" for="phone">
								<?= GetMessage("TITLE_TAB_CLIENT_FORM_PASSPORT_INPUT_PHONE") ?><span>*</span>
							</label>
							<input class="form-control" type="tel" name="phone" id="phone" required>
						</div>
					</div>
				</div>
	
				<div class="inputs-group">
					<h4 class="title-secondary"><?= GetMessage("TITLE_TAB_CLIENT_FORM_ARREARS_DATA") ?></h4>
					<div class="row">
						<div class="col-3">
							<label class="form-label" for="guarantors_cobligors">
								<?= GetMessage("TITLE_TAB_CLIENT_FORM_ARREARS_GUARANTORS_COBLIGORS") ?>
							</label>
							<input class="form-control" type="text" name="guarantors_cobligors" id="guarantors_cobligors">
						</div>
						<div class="col-3">
							<label class="form-label" for="delinquencies_at_contract">
								<?= GetMessage("TITLE_TAB_CLIENT_FORM_ARREARS_DELINQUENCIES_AT_CONTRACT") ?>
							</label>
							<input class="form-control" type="text" name="delinquencies_at_contract" id="delinquencies_at_contract">
						</div>
						<div class="col-3">
							<label class="form-label" for="overdue_mfo">
								<?= GetMessage("TITLE_TAB_CLIENT_FORM_ARREARS_OVERDUE_MFO") ?>
							</label>
							<input class="form-control" type="text" name="overdue_mfo" id="overdue_mfo">
						</div>
					</div>
	
					<div class="row">
						<div class="col-3">
							<label class="form-label" for="overdue_housing_services">
								<?= GetMessage("TITLE_TAB_CLIENT_FORM_ARREARS_OVERDUE_HOUSING_SERVICES") ?>
							</label>
							<input class="form-control" type="text" name="overdue_housing_services" id="overdue_housing_services">
						</div>
						<div class="col-3">
							<label class="form-label" for="creditors_number">
								<?= GetMessage("TITLE_TAB_CLIENT_FORM_ARREARS_CREDITORS_NUMBER") ?>
							</label>
							<input class="form-control" type="text" name="creditors_number" id="creditors_number">
						</div>
						<div class="col-3">
							<label class="form-label" for="judicial_acts">
								<?= GetMessage("TITLE_TAB_CLIENT_FORM_ARREARS_JUDICIAL_ACTS") ?>
							</label>
							<input class="form-control" type="text" name="judicial_acts" id="judicial_acts">
						</div>
					</div>
	
					<div class="row">
						<div class="col-3">
							<label class="form-label" for="total_encumbrance_amount">
								<?= GetMessage("TITLE_TAB_CLIENT_FORM_ARREARS_TOTAL_ENCUMBRANCE_AMOUNT") ?>
							</label>
							<input class="form-control" type="text" name="total_encumbrance_amount" id="total_encumbrance_amount">
						</div>
						<div class="col-3">
							<label class="form-label" for="enforcement_proceedings">
								<?= GetMessage("TITLE_TAB_CLIENT_FORM_ARREARS_ENFORCEMENT_PROCEEDINGS") ?>
							</label>
							<input class="form-control" type="text" name="enforcement_proceedings" id="enforcement_proceedings">
						</div>
						<div class="col-3">
							<label class="form-label" for="tax_liabilities">
								<?= GetMessage("TITLE_TAB_CLIENT_FORM_ARREARS_TAX_LIABILITIES") ?>
							</label>
							<input class="form-control" type="text" name="tax_liabilities" id="tax_liabilities">
						</div>
					</div>
				</div>
	
				<div class="inputs-group">
					<div class="row">
						<div class="col-6">
							<label class="form-label" for="notes">
								<?= GetMessage("TITLE_TAB_CLIENT_TEXTAREA_NOTES") ?>
							</label>
							<textarea class="form-control" name="notes" id="notes"></textarea>
						</div>
					</div>
					<div class="row">
						<div class="col-6">
							<label class="form-label" for="change_history">
								<?= GetMessage("TITLE_TAB_CLIENT_TEXTAREA_CHANGE_HISTORY") ?>
							</label>
							<textarea class="form-control" name="change_history" id="change_history"></textarea>
						</div>
					</div>
					<div class="row">
						<div class="col-6">
							<label class="form-label" for="work_plan">
								<?= GetMessage("TITLE_TAB_CLIENT_TEXTAREA_WORK_PLAN") ?>
							</label>
							<textarea class="form-control" name="work_plan" id="work_plan"></textarea>
						</div>
					</div>
				</div>
	
				<button type="button" data-next-step="step-payments" class="btn btn-primary">Далее</button>
			</div>
	
			<div class="block" data-step="step-payments">
				<div class="row">
					<div class="col-3">
						<label class="form-label" for="contract_amount">
							<?= GetMessage("TITLE_TAB_SUM_PAYMENT_SCHEDULE_CONTRACT_AMOUNT") ?><span>*</span>
						</label>
						<input class="form-control" type="text" name="contract_amount" id="contract_amount" required>
					</div>
					<div class="col-3">
						<label class="form-label" for="contract_term">
							<?= GetMessage("TITLE_TAB_SUM_PAYMENT_SCHEDULE_CONTRACT_TERM") ?><span>*</span>
						</label>
						<input class="form-control" type="date" name="contract_term" id="contract_term" required>
					</div>
				</div>
				<div class="row">
					<div class="col-3">
						<label class="form-label" for="deferment">
							<?= GetMessage("TITLE_TAB_SUM_PAYMENT_SCHEDULE_DEFERMENT") ?>
						</label>
						<input class="form-control" type="text" name="deferment" id="deferment">
					</div>
					<div class="col-3">
						<label class="form-label" for="prepayment">
							<?= GetMessage("TITLE_TAB_SUM_PAYMENT_SCHEDULE_PREPAYMENT") ?>
						</label>
						<input class="form-control" type="text" name="prepayment" id="prepayment">
					</div>
				</div>
				<div>
					<button type="button" id="form-payment" class="btn btn-secondary mt-3">Сформировать график платежей</button>
					
					<table id="table-payments" class="table main-table mt-4">
						<thead>
							<tr class="table-secondary">
								<th scope="col">№ Платежа</th>
								<th scope="col">Дата платежа</th>
								<th scope="col">Сумма платежа(рубли)</th>
								<th scope="col">Почтовые расходы(рубли)</th>
							</tr>
						</thead>
						<tbody>

						</tbody>
						<tfoot>
							<tr>
								<td colspan="2">Итого</td>
								<td id="total-payment"></td>
								<td id="total-postal"></td>
							</tr>
						</tfoot>
					</table>
				</div>
				<button type="button" data-next-step="step-creditor" class="btn btn-primary my-2">Далее</button>
			</div>
	
			<div class="block" data-step="step-creditor">
				<div class="creditor-wrapper">
					<div class="row">
						<div class="col-3">
							<? if (!empty($optionsCreditors)): ?>
								<label class="form-label" for="creditor">
									<?= GetMessage("TITLE_TAB_CREDITORS_NAME") ?>
								</label>
								<select class="form-select" name="creditor[1][id]" id="creditor">
									<? foreach ($optionsCreditors as $key => $option): ?>
										<option value="<?= $key ?>"><?= $option ?></option>
									<? endforeach; ?>
								</select>
							<? endif; ?>
						</div>
						<div class="col-2">
							<label class="form-label" for="creditor[1][number]">
								<?= GetMessage("TITLE_TAB_CONTRACT_INPUT_NUMBER") ?>
							</label>
							<input class="form-control" type="text" name="creditor[1][number]">
						</div>
						<div class="col-2">
							<label class="form-label" for="creditor[1][date]">
								<?= GetMessage("TITLE_TAB_CONTRACT_INPUT_DATE") ?>
							</label>
							<input class="form-control" type="date" name="creditor[1][date]">
						</div>
						<div class="col-1 d-flex align-items-center">
							<button type="button" class="delete-creditor pt-4">X</button>
						</div>
					</div>
				</div>
				<button type="button" class="btn btn-secondary add-creditor-btn mt-3">+Добавить кредитора</button>
			</div>
		</form>
	</div>
</main>
<script src="/local/templates/crm-my-prava/js/steps.js"></script>
<script>
	$(function() {
		$('#form-payment').on('click', function() {
			$('#table-payments tbody').html('')
			let date = $('#contract_term').val()
			var today = new Date()
			var	testDate = new Date(date)
			var deferment = $('#deferment').val()
			console.log(deferment)
			if (deferment != '') {
				today.setMonth(today.getMonth() + Number(deferment)) 
			}
			var	todayMonth = today.getMonth()
			var testMonth = testDate.getMonth()
			var	todayYear = today.getFullYear()
			var	testYear = testDate.getFullYear()
		

			var term = testMonth - todayMonth + (testYear - todayYear) * 12
			var amount = $('#contract_amount').val() - $('#prepayment').val()
			var monthPay = amount / (term + 1)

			for (let i = 1; i <= term + 1; i++ ) {
				let date = today
				if (i !== 1) {
					date.setMonth(date.getMonth() + 1)
				}

				let month = date.getMonth() + 1
				if (month < 10) {
					month = `0${month}`
				}
				let day = date.getDate()
				if (day < 10) {
					day = `0${day}`
				}
				const indexDate = `${date.getFullYear()}-${month}-${day}`
			
				$('#table-payments tbody').append(`
					<tr>
						<td>
							${i}
						</td>
						<td>
							<input value=${indexDate} class="form-control" type="date" name="payment[${i}][date]">
						</td>
						<td>
							<input class="form-control inp-sum" value=${monthPay.toFixed(2)} type="number" name="payment[${i}][sum-payment]">
						</td>
						<td>
							<input class="form-control inp-pos" type="number" name="payment[${i}][sum-pochta]">
						</td>
					</tr>
				`)
			}

			getTotal()

			$('.inp-sum').on('blur', function() {
			console.log('fwf')
			getTotal()
			})
			$('.inp-pos').on('blur', function() {
				getTotal()
			})
		})

		function getTotal() {
			var totalSum = 0
			var totalPal = 0
			$('.inp-sum').each(function() {
				totalSum += Number($(this).val())
			})
			$('.inp-pos').each(function() {
				totalPal += Number($(this).val())
			})

			$('#total-payment').html(totalSum)
			$('#total-postal').html(totalPal)
		}

		// creditors
		let index = 1

		function addCreditor() {
			index++
			$('.creditor-wrapper').append(`
				<div class="row">
					<div class="col-3">
						<? if (!empty($optionsCreditors)): ?>
							<label class="form-label" for="creditor">
								<?= GetMessage("TITLE_TAB_CREDITORS_NAME") ?>
							</label>
							<select class="form-select" name="creditor[${index}][id]" id="creditor">
								<? foreach ($optionsCreditors as $key => $option): ?>
									<option value="<?= $key ?>"><?= $option ?></option>
								<? endforeach; ?>
							</select>
						<? endif; ?>
					</div>
					<div class="col-2">
						<label class="form-label" for="creditor[${index}][number_contract]">
							<?= GetMessage("TITLE_TAB_CONTRACT_INPUT_NUMBER") ?>
						</label>
						<input class="form-control" type="text" name="creditor[${index}][number_contract]">
					</div>
					<div class="col-2">
						<label class="form-label" for="creditor[${index}][date_contract]">
							<?= GetMessage("TITLE_TAB_CONTRACT_INPUT_DATE") ?>
						</label>
						<input class="form-control" type="date" name="creditor[${index}][date_contract]">
					</div>
					<div class="col-1 d-flex align-items-center">
						<button type="button" class="delete-creditor pt-4">X</button>
					</div>
				</div>
			`)
			$('.delete-creditor').each(function() {
				$(this).on('click', function() {
					$(this).closest('.row').remove()
				})
			})
		}

		$('.add-creditor-btn').on('click', function() {
			addCreditor()
		})

		$('.delete-creditor').on('click', function() {
			console.log($(this).closest('.row').remove())
		})
	});
</script>


<? require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php'); ?>


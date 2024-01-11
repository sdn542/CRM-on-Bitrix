<?
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');
IncludeTemplateLangFile(__FILE__);
$APPLICATION->SetTitle('Создание задачи');
$APPLICATION->SetPageProperty("description", 'Создание задачи');

use Bitrix\Main\Loader;
use Bitrix\Iblock\PropertyEnumerationTable;
use Bitrix\Iblock\IblockTable;
?>
<main class="main">
	<div class="container">
		<form method="post" action="/ajax/tasks/add.php" enctype="multipart/form-data">
			<div class="d-flex justify-content-between align-items-center mb-5">
				<h1 class="title">Создание задачи</h1>
				<div class="buttons">
					<button class="btn btn-primary" type="submit">Сохранить</button>
					<a class="btn btn-secondary" href="/lawyer/tasks">Отмена</a>
				</div>
			</div>
			<div class="block">
				<div class="row mb-3">
					<div class="col-5">
						<div class="row mb-3">
							<div class="col-6">
								<label class="form-label" for="client">
									ФИО Клиента<span>*</span>
								</label>
								<select class="form-select clients-select" name="client" required>
									<?
									$groupCode = "clients";
									$rsGroup = CGroup::GetList($by = "c_sort", $order = "asc", array("STRING_ID" => $groupCode));
									$arGroup = $rsGroup->Fetch();
									$groupId = $arGroup["ID"];
					
									if ($groupId) {
										$userFilter = array("GROUPS_ID" => array($groupId));
										$userParams = array("SELECT" => array("ID", "NAME", "EMAIL"));
					
										$rsUsers = CUser::GetList($by = "ID", $order = "asc", $userFilter, $userParams);
					
										while ($arUser = $rsUsers->Fetch()) {?>
											<option value="<?= $arUser["ID"] ?>"><?= $arUser["LAST_NAME"] . " " . $arUser["NAME"] . (!empty($arUser["SECOND_NAME"]) ? " " . $arUser["SECOND_NAME"] : "") ?></option>
										<?}
									}
									?>
								</select>
							</div>
							<div class="col-6">
								<label class="form-label" for="contract">
									№ Договора<span>*</span>
								</label>
								<select id="contracts" class="form-select" name="contract" required>
									<!--Получение договоров с помощью запроса-->
								</select>
							</div>
						</div>
						<div class="row mb-3 type-row-no d-none">
							<div class="col-12">
								<label class="form-label" for="type">
									Тип<span>*</span>
								</label>
								<input name="type" placeholder="Свой тип">
							</div>
						</div>
						<div class="row mb-3 type-row">
							<div class="col-12">
								<select class="form-select no-select" name="type" required>
										<option value="Отказ от взаимодействия с третьими лицами">Отказ от взаимодействия с третьими лицами</option>
								</select>
							</div>
						</div>
						<div class="row mb-3">
							<div class="col-12">
								<input class="form-check-input non-standart-input" type="checkbox" name="non_standart" value="Y">
								<label class="form-check-label" for="non_standart">
									Нестандартная задача
								</label>
							</div>
						</div>
						<div class="row mb-3">
							<div class="col-6">
								<label class="form-label" for="type_post_item">
									Вид почтового отправления
								</label>
								<select class="form-select" name="type_post_item">
									<option>Выбрать</option>
									<?
									$property_enums_tasks = CIBlockPropertyEnum::GetList(Array("ID"=>"ASC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$iblock["ID"], "CODE" => "TYPE_POST_ITEM"));
					
									while($enum_fields_tasks = $property_enums_tasks->GetNext())
									{?>
										<option value="<?= $enum_fields_tasks["ID"] ?>"><?= $enum_fields_tasks["VALUE"] ?></option>
									<?} ?>
								</select>
							</div>
						</div>
						<div class="row mb-3">
							<div class="col-6">
								<label class="form-label" for="deadline">
									Крайний срок<span>*</span>
								</label>
								<input class="form-control" type="date" name="deadline">
							</div>
						</div>
						<div class="row mb-3">
							<div class="col6">
								<label class="form-label" for="active_from">
									Дата начала<span>*</span>
								</label>
								<input class="form-control" type="date" name="active_from">
							</div>
						</div>
						<div class="row mb-3">
							<div class="col-6">
								<label class="form-label" for="interim_period">
									Промежуточный срок<span>*</span>
								</label>
								<input class="form-control" type="date" name="interim_period">
							</div>
						</div>
						<div class="row mb-3">
							<div class="col-12">
								<input class="form-check-input" type="checkbox" name="show_in_lk" value="Y">
								<label class="form-check-label" for="show_in_lk">
									Отобразить задачу в личном кабинете клиента
								</label>
							</div>
						</div>
						<div class="row mb-3">
							<div class="col-12">
								<label class="form-label" for="description">
									Описание
								</label>
								<textarea class="form-control" name="description"></textarea>
							</div>
						</div>
						<div class="row mb-3">
							<div class="col-12">
								<label class="form-label" for="lawyer">
									Исполнитель<span>*</span>
								</label>
								<select class="form-select" name="lawyer" required>
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
					</div>
					<div class="col-7">
						<div class="table-responsive">
							<table class="table main-table">
								<thead class="table-secondary">
									<tr>
										<th scope="col">Адресат</th>
										<th scope="col">Адрес</th>
										<th scope="col">Track-номер</th>
										<th scope="col">Документ</th>
										<th scope="col">Ответ</th>
									</tr>
								</thead>
								<tbody class="creditors-wrapper">

								</tbody>
							</table>
						</div>	
						<button class="btn btn-secondary mt-3 add-creditor-btn" type="button">+ Добавить адресата</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</main>

<script>
	$(function() {
		$('.non-standart-input').on('change', function() {
			$('.type-row-no').toggleClass('d-none')
			$('.type-row').toggleClass('d-none')
			if ($('.type-row').hasClass('d-none')) {
				$('.no-select').attr('disabled', 'true')
			} else {
				$('.no-select').removeAttr('disabled')
			}
		})
		
		
		function getOptions(url, select, value = 0) {
			var id = 0
			if (value > 0) {
				id = value
			} else {
				id = $('.clients-select option')[0].value
			}

			$.ajax({
				type: "POST",
				url: url,
				data: {client: id}
			}).done(function(response) {
				$(select).html('')
				result = jQuery.parseJSON(response)
				result.data.map(function(item) {
					const key = Object.keys(item)[0]
					const value = item[key]
					$(select).append(`<option value=${key}>${value}</option>`)
				})
			}).fail(function() {
				console.log('fail');
			});
		}

		getOptions("/ajax/tasks/get_contracts.php", '#contracts')

		$('.clients-select').on('change', function(e) {
			var id = e.target.value
			console.log(id)
			getOptions("/ajax/tasks/get_contracts.php", '#contracts', id)
		})

		function getCreditors(select) {
			$.ajax({
				type: "POST",
				url: '/ajax/tasks/get_creditors.php',
				data: 'text'
			}).done(function(response) {
				result = jQuery.parseJSON(response)
				result.data.map(function(item) {
					const key = Object.keys(item)[0]
					const value = item[key]
					$(select).append(`<option data-address="${value.address}" value=${key}>${value.name}</option>`)
				})
			}).fail(function() {
				console.log('fail');
			});
		}

		let creditorIndex = 0

		function addCreditor() {
			creditorIndex++
			$('.creditors-wrapper').append(`
			<tr>
				<td><select style="width: 150px" class="form-select sel-${creditorIndex}" id="creditors" name="creditor[${creditorIndex}][id]"></select></td>
				<td data-index="${creditorIndex}"></td>
				<td><input style="width: 150px" class="form-control" name="creditor[${creditorIndex}][track]" type="text"></td>
				<td><input style="width: 300px" class="form-control" name="creditor[${creditorIndex}][doc]" type="file"></td>
				<td><input style="width: 300px" class="form-control" name="creditor[${creditorIndex}][response]" type="file"></td>
			`)
			var select = $(`.sel-${creditorIndex}`)
			var address = $(`td[data-index=${creditorIndex}]`)
			getCreditors(select)
			setTimeout(() => {
				var addrValue = $(`.sel-${creditorIndex} option:selected`).attr('data-address')
				address.html(addrValue)
			}, 200);
		}

		$('.add-creditor-btn').on('click', function() {
			addCreditor()
		})

	});
</script>
<? require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php'); ?>
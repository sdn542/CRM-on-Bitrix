<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
use Bitrix\Iblock\IblockTable;
$arCreator = CUser::GetByID($arResult["PROPERTIES"]["CREATOR"]["VALUE"])->Fetch();
$arLawyer = CUser::GetByID($arResult["PROPERTIES"]["LAWYER"]["VALUE"])->Fetch();
$arClient = CUser::GetByID($arResult["PROPERTIES"]["CLIENT"]["VALUE"])->Fetch();
if(!empty($arResult["PROPERTIES"]["CONTRACT"]["VALUE"]))
{
	$resContract = CIBlockElement::GetByID($arResult["PROPERTIES"]["CONTRACT"]["VALUE"])->GetNextElement();
	if($resContract)
	{
		$arContract = array(
			"FIELDS" => $resContract->GetFields(),
			"PROPERTIES" => $resContract->GetProperties()
		);
	}
}
?>
<main class="main">
	<div class="container">
		<div class="d-flex justify-content-between align-items-center">
			<h1 class="title"><?= $arResult["PROPERTIES"]["TYPE_TASK"]["VALUE"] ?></h1>
			<div class="d-flex align-items-center">
				<button class="btn btn-danger me-2" data-bs-target="#confirmDeleteModal" data-bs-toggle="modal">Удалить</button>
				<a class="btn btn-secondary me-2" href="/lawyer/tasks/">Отмена</a>
				<button class="btn btn-primary fake-submit">Сохранить</button>
			</div>
		</div>
		<div class="create-client-tabs">
			<h3 data-step-tab="task" class="task-tab">Задача</h3>
			<h3 data-step-tab="history" class="task-tab">История</h3>
		</div>
		<div data-step="task" class="block active-step">
			<div class="row">
				<div class="col-6">
					<form class="save-task" method="post" action="/ajax/tasks/save_task.php">
						<input style="opacity:0; visibility: hidden; position: absolute" type="submit" value="Сохранить">
						<input type="hidden" name="task_id" value="<?= $arResult["ID"] ?>">
						<div class="info">
							<div>Постановщик</div><div></div><div><?= $arCreator["LAST_NAME"] . " " . $arCreator["NAME"] . (!empty($arCreator["SECOND_NAME"]) ? " " . $arCreator["SECOND_NAME"] : "") ?></div>
						</div>
						<div class="info">
							<div>Исполнитель</div><div></div><div><?= $arLawyer["LAST_NAME"] . " " . $arLawyer["NAME"] . (!empty($arLawyer["SECOND_NAME"]) ? " " . $arLawyer["SECOND_NAME"] : "") ?></div>
						</div>
						<div class="info">
							<div>Клиент</div><div></div><div><?= $arClient["LAST_NAME"] . " " . $arClient["NAME"] . (!empty($arClient["SECOND_NAME"]) ? " " . $arClient["SECOND_NAME"] : "") ?></div>
						</div>
						<div class="info">
							<div>№ Договора</div><div></div><div><?= $arContract["PROPERTIES"]["NUMBER"]["VALUE"] ?></div>
						</div>
						<div class="info">
							<div>Дата начала</div><div></div><div> <?= $arResult["ACTIVE_FROM"] ?></div>
						</div>
						<div class="info">
							<div>Промежуточный срок</div><div></div><div><input type="date" name="interim_period" <?= checkAccessElement($arResult["ID"]) ? "" : "disabled" ?> value="<?= date("Y-m-d", strtotime($arResult["PROPERTIES"]["INTERIM_DATE"]["VALUE"])) ?>" required></div>
						</div>
						<div class="info">
							<div>Крайний срок</div><div></div><div><input type="date" name="deadline" <?= checkAccess(1) ? "" : "disabled" ?> value="<?= date("Y-m-d", strtotime($arResult["PROPERTIES"]["DEADLINE"]["VALUE"])) ?>"></div>
						</div>
	
						<div>
							<label class="form-label" for="type_post_item">
								Вид почтового отправления
							</label>
							<select class="form-select" name="type_post_item" id="type_post_item" <?= checkAccess(1) ? "" : "disabled" ?>>
								<option>Выбрать</option>
								<?
								$property_enums_tasks = CIBlockPropertyEnum::GetList(Array("ID"=>"ASC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$arResult["IBLOCK_ID"], "CODE" => "TYPE_POST_ITEM"));
								while($enum_fields_tasks = $property_enums_tasks->GetNext())
								{?>
									<option value="<?= $enum_fields_tasks["ID"] ?>" <?= $arResult["PROPERTIES"]["TYPE_POST_ITEM"]["VALUE_ENUM_ID"] == $enum_fields_tasks["ID"] ? "selected" : "" ?>><?= $enum_fields_tasks["VALUE"] ?></option>
								<?} ?>
							</select>
						</div>
						<div>
						<div class="my-2">
							<input id="show_in_lk" type="checkbox" name="show_in_lk" value="Y" <?= checkAccess(1) ? "" : "disabled" ?> <?= $arResult["PROPERTIES"]["SHOW_IN_LK"]["VALUE"]=="Y" ? "checked" : "" ?>>
								<label class="form-check-label" for="nshow_in_lk">
									Отобразить задачу в личном кабинете клиента
								</label>
							</div>
						</div>
						
						<h2 class="title-secondary">Статус задачи</h2>
						<div class="d-flex">
							<?
							$property_enums = CIBlockPropertyEnum::GetList(Array("ID"=>"ASC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$arResult["IBLOCK_ID"], "CODE" => "STATUS_TASK"));
							while($enum_fields = $property_enums->GetNext())
							{?>
								<div class="form-check me-2">
									<input
											class="form-check-input"
											type="radio"
											name="status_task"
										<?= checkAccessElement($arResult["ID"]) ? "" : "disabled" ?>
											id="<?= $enum_fields["ID"] ?>"
											value="<?= $enum_fields["ID"] ?>"
										<?= $arResult["PROPERTIES"]["STATUS_TASK"]["VALUE_ENUM_ID"] == $enum_fields["ID"] ? "checked" : "" ?>
											style="float: left"
									>
									<label class="form-check-label" for="<?= $enum_fields["ID"] ?>">
										<?= $enum_fields["VALUE"] ?>
									</label>
								</div>
							<?} ?>
						</div>
						<h2 class="title-secondary">Статус почты</h2>
						<div class="d-flex">
							<?
							$property_enums = CIBlockPropertyEnum::GetList(Array("ID"=>"ASC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$arResult["IBLOCK_ID"], "CODE" => "STATUS_POST"));
							while($enum_fields = $property_enums->GetNext())
							{?>
								<div class="form-check me-2">
									<input
											class="form-check-input"
											type="radio"
											name="status_post"
										<?= checkAccess(0) ? "" : "disabled" ?>
											id="<?= $enum_fields["ID"] ?>"
											value="<?= $enum_fields["ID"] ?>"
										<?= $arResult["PROPERTIES"]["STATUS_POST"]["VALUE_ENUM_ID"] == $enum_fields["ID"] ? "checked" : "" ?>
											style="float: left"
									>
									<label class="form-check-label" for="<?= $enum_fields["ID"] ?>">
										<?= $enum_fields["VALUE"] ?>
									</label>
								</div>
							<?} ?>
						</div>
						<h2 class="title-secondary">Описание</h2>
						<textarea class="form-control" name="description" <?= checkAccessElement($arResult["ID"]) ? "" : "disabled" ?>><?= $arResult["PREVIEW_TEXT"] ?></textarea>
	
						<div>
							<label class="form-label" for="lawyer-select">
								Исполнитель
							</label>
							<select class="form-select" id="lawyer-select" name="lawyer" <?= checkAccessElement($arResult["ID"]) ? "" : "disabled" ?>>
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
										<option value="<?= $arUser["ID"] ?>" <?= $arResult["PROPERTIES"]["LAWYER"]["VALUE"] == $arUser["ID"] ? "selected" : "" ?>><?= $arUser["LAST_NAME"] . " " . $arUser["NAME"] . (!empty($arUser["SECOND_NAME"]) ? " " . $arUser["SECOND_NAME"] : "") ?></option>
									<?}
								}
								?>
							</select>
						</div>
					</form>
				</div>
				<div class="col-6">
					<?
						global $arFilter;
						$arFilter = array("ID" => $arResult["PROPERTIES"]["ADDRESSEES"]["VALUE"]);
						$APPLICATION->IncludeComponent(
							"bitrix:news.list",
							"departures-task",
							Array(
								"ACTIVE_DATE_FORMAT" => "d.m.Y",
								"ADD_SECTIONS_CHAIN" => "Y",
								"AJAX_MODE" => "N",
								"AJAX_OPTION_ADDITIONAL" => "",
								"AJAX_OPTION_HISTORY" => "N",
								"AJAX_OPTION_JUMP" => "N",
								"AJAX_OPTION_STYLE" => "Y",
								"CACHE_FILTER" => "N",
								"CACHE_GROUPS" => "Y",
								"CACHE_TIME" => "36000000",
								"CACHE_TYPE" => "A",
								"CHECK_DATES" => "Y",
								"DETAIL_URL" => "",
								"DISPLAY_BOTTOM_PAGER" => "Y",
								"DISPLAY_DATE" => "Y",
								"DISPLAY_NAME" => "Y",
								"DISPLAY_PICTURE" => "Y",
								"DISPLAY_PREVIEW_TEXT" => "Y",
								"DISPLAY_TOP_PAGER" => "N",
								"FIELD_CODE" => array("", ""),
								"FILTER_NAME" => "arFilter",
								"HIDE_LINK_WHEN_NO_DETAIL" => "N",
								"IBLOCK_ID" => "departures",
								"IBLOCK_TYPE" => "content",
								"INCLUDE_IBLOCK_INTO_CHAIN" => "Y",
								"INCLUDE_SUBSECTIONS" => "Y",
								"MESSAGE_404" => "",
								"NEWS_COUNT" => "14",
								"PAGER_BASE_LINK_ENABLE" => "N",
								"PAGER_DESC_NUMBERING" => "N",
								"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
								"PAGER_SHOW_ALL" => "N",
								"PAGER_SHOW_ALWAYS" => "N",
								"PAGER_TEMPLATE" => ".default",
								"PAGER_TITLE" => "Новости",
								"PARENT_SECTION" => "",
								"PARENT_SECTION_CODE" => "",
								"PREVIEW_TRUNCATE_LEN" => "",
								"PROPERTY_CODE" => array("CLIENT", "NUMBER", "GRACE_PERIOD", "ADVANCE_PAYMENT", "CONTRACT_TERM", "STATUS", "CONTRACT_AMOUNT", "TYPE", "LAWYER", ""),
								"SET_BROWSER_TITLE" => "Y",
								"SET_LAST_MODIFIED" => "N",
								"SET_META_DESCRIPTION" => "Y",
								"SET_META_KEYWORDS" => "Y",
								"SET_STATUS_404" => "N",
								"SET_TITLE" => "Y",
								"SHOW_404" => "N",
								"SORT_BY1" => "ACTIVE_FROM",
								"SORT_BY2" => "SORT",
								"SORT_ORDER1" => "DESC",
								"SORT_ORDER2" => "ASC",
								"STRICT_SECTION_CHECK" => "N",
								"TASK_ID" => $arResult["ID"]
							)
						);
					?>
					<div class="d-flex">
						<button class="btn btn-primary" data-bs-target="#creditorsModal" data-bs-toggle="modal">+ Добавить адресата</button>
						<form enctype="multipart/form-data" method="post" action="/ajax/tasks/docx_handler.php">
							<input type="text" name="task_id" value="<?= $arResult["ID"] ?>" hidden>
							<input class="btn btn-secondary ms-2" type="submit" value="Сформировать документы">
						</form>
					</div>
					<div class="modal fade" id="creditorsModal" aria-hidden="true" aria-labelledby="docsForClientLabel" tabindex="-1">
						<div class="modal-dialog modal-dialog-centered">
							<div class="modal-content">
								<div class="modal-body">
									<h3 class="title" id="docsForClientLabel">Список кредиторов</h3>
									<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
									<? if(checkAccess(2)): ?>
										<form class="d-flex flex-column" method="post" action="/ajax/tasks/add_creditor.php">
											<input type="hidden" name="task_id" value="<?= $arResult["ID"] ?>">
											<label class="form-label" for="creditor">
												Наименование
											</label>
											<select class="form-select" id="creditor" name="creditor">
												<?
												$arFilter = array(
													"IBLOCK_CODE" => "creditors",
													"IBLOCK_TYPE" => "content",
												);
												if(empty($arResult["PROPERTIES"]["NON_STANDART"]["VALUE"])){
													$arFilter["ID"] = $arContract["PROPERTIES"]["CREDITOR"]["VALUE"];
												}
												$arSelect = array("ID", "NAME");
												$rsElements = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
												while ($arElement = $rsElements->Fetch()) {?>
													<option value="<?= $arElement["ID"] ?>"><?= $arElement["NAME"] ?></option>
												<?} ?>
											</select>
											<input class="btn btn-primary mx-auto mt-3" type="submit" value="Добавить">
										</form>
									<? endif; ?>
								</div>
							</div>
						</div>
					</div>
					<div>
						<?= CFile::GetFileArray($arResult["PROPERTIES"]["DOC"]["VALUE"])["ORIGINAL_NAME"] ?><a href="<?= CFile::GetPath($arResult["PROPERTIES"]["DOC"]["VALUE"]) ?>" download>Скачать</a>
					</div>
					<? if(!empty($arResult["PROPERTIES"]["NON_STANDART"]["VALUE"])): ?>
					<? if(!empty($arResult["PROPERTIES"]["TEMPLATE"]["VALUE"])): ?>
						<div class="d-flex gap-2 pb-2 my-3" style="border-bottom: 1px solid #747474; width: max-content">
							<div>
								<img src="/local/templates/crm-my-prava/images/pdf-icon.png" alt="">
								<span><?= CFile::GetFileArray($arResult["PROPERTIES"]["TEMPLATE"]["VALUE"])["ORIGINAL_NAME"] ?></span>
								<a href="<?= CFile::GetPath($arResult["PROPERTIES"]["TEMPLATE"]["VALUE"]) ?>" download><img src="/local/templates/crm-my-prava/images/download-icon.png" alt=""></a>
							</div>
							<? if(checkAccess(2)): ?>
							<form action="/ajax/tasks/delete_check.php" method="post">
								<input type="text" name="task_id" value="<?= $arResult["ID"] ?>" hidden>
								<input type="text" name="file_id" value="<?= $arResult["PROPERTIES"]["TEMPLATE"]["VALUE"] ?>" hidden>
								<input type="text" name="property_code" value="TEMPLATE" hidden>
								<input class="btn p-0" type="submit" value="x">
							</form>
							<? endif; ?>
						</div>
					<?else:?>
						<form class="my-3" enctype="multipart/form-data" method="post" action="/ajax/tasks/add_check.php">
							<input class="form-control" type="text" name="task_id" value="<?= $arResult["ID"] ?>" hidden>
							<input class="form-control" type="text" name="property_code" value="TEMPLATE" hidden>
							<input class="form-control" type="file" name="files[]" multiple>
							<input class="btn btn-primary mt-1" type="submit">
						</form>
					<?endif;?>
					<?endif;?>
					<div style="width: 340px" class="mt-4">
						<table class="table main-table">
							<thead>
								<tr class="table-secondary">
									<th scope="col">Дата загрузки</th>
									<th scope="col">Чек</th>
								</tr>
							</thead>
							<tbody>
								<? foreach ($arResult["PROPERTIES"]["CHECK_FILES"]["VALUE"] as $index => $doc): ?>
									<tr>
										<td><?= $arResult["PROPERTIES"]["CHECK_FILES"]["DESCRIPTION"][$index] ?></td>
										<td>
											<div class="d-flex align-items-center gap-2">
												<?= CFile::GetFileArray($doc)["ORIGINAL_NAME"] ?>
												<a href="<?= CFile::GetPath($doc) ?>" download><img src="/local/templates/crm-my-prava/images/download-icon.png" alt=""></a>
												<? if(checkAccess(2)): ?>
												<form action="/ajax/tasks/delete_check.php" method="post">
													<input type="text" name="task_id" value="<?= $arResult["ID"] ?>" hidden>
													<input type="text" name="file_id" value="<?= $doc ?>" hidden>
													<input type="text" name="property_code" value="CHECK_FILES" hidden>
													<input class="btn p-0" type="submit" value="x">
												</form>
												<? endif; ?>
											</div>
										</td>
									</tr>
								<? endforeach; ?>
							</tbody>
						</table>
						<button class="btn btn-primary" data-bs-target="#addCheckModal" data-bs-toggle="modal">Добавить чек</button>
						<div class="modal fade" id="addCheckModal" aria-hidden="true" aria-labelledby="docsForClientLabel" tabindex="-1">
							<div class="modal-dialog modal-dialog-centered">
								<div class="modal-content">
									<div class="modal-body">
										<h3 class="title" id="docsForClientLabel">Добавить чек</h3>
										<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
										<form enctype="multipart/form-data" method="post" action="/ajax/tasks/add_check.php">
											<input class="form-control" type="text" name="task_id" value="<?= $arResult["ID"] ?>" hidden>
											<input class="form-control" type="text" name="property_code" value="CHECK_FILES" hidden>
											<input class="form-control" type="file" name="files[]" multiple>
											<input class="btn btn-primary mt-2" type="submit">
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
					
				</div>
			</div>
		</div>
		<div data-step="history" class="row block">
			<div>
				<?
				global $arFilter;
				$arFilter = array(
					"=PROPERTY_TASK" => $arResult["ID"]
				);
				$APPLICATION->IncludeComponent(
					"bitrix:news.list",
					"lawyer-task-history",
					Array(
						"ACTIVE_DATE_FORMAT" => "d.m.Y",
						"ADD_SECTIONS_CHAIN" => "Y",
						"AJAX_MODE" => "N",
						"AJAX_OPTION_ADDITIONAL" => "",
						"AJAX_OPTION_HISTORY" => "N",
						"AJAX_OPTION_JUMP" => "N",
						"AJAX_OPTION_STYLE" => "Y",
						"CACHE_FILTER" => "N",
						"CACHE_GROUPS" => "Y",
						"CACHE_TIME" => "36000000",
						"CACHE_TYPE" => "A",
						"CHECK_DATES" => "Y",
						"DETAIL_URL" => "",
						"DISPLAY_BOTTOM_PAGER" => "Y",
						"DISPLAY_DATE" => "Y",
						"DISPLAY_NAME" => "Y",
						"DISPLAY_PICTURE" => "Y",
						"DISPLAY_PREVIEW_TEXT" => "Y",
						"DISPLAY_TOP_PAGER" => "N",
						"FIELD_CODE" => array("", ""),
						"FILTER_NAME" => "arFilter",
						"HIDE_LINK_WHEN_NO_DETAIL" => "N",
						"IBLOCK_ID" => "history",
						"IBLOCK_TYPE" => "content",
						"INCLUDE_IBLOCK_INTO_CHAIN" => "Y",
						"INCLUDE_SUBSECTIONS" => "Y",
						"MESSAGE_404" => "",
						"NEWS_COUNT" => !empty($_REQUEST["limit"]) ? $_REQUEST["limit"] : "20",
						"PAGER_BASE_LINK_ENABLE" => "N",
						"PAGER_DESC_NUMBERING" => "N",
						"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
						"PAGER_SHOW_ALL" => "N",
						"PAGER_SHOW_ALWAYS" => "N",
						"PAGER_TEMPLATE" => ".default",
						"PAGER_TITLE" => "Новости",
						"PARENT_SECTION" => "",
						"PARENT_SECTION_CODE" => "",
						"PREVIEW_TRUNCATE_LEN" => "",
						"PROPERTY_CODE" => array("CLIENT", "NUMBER", "GRACE_PERIOD", "ADVANCE_PAYMENT", "CONTRACT_TERM", "STATUS", "CONTRACT_AMOUNT", "TYPE", "LAWYER", ""),
						"SET_BROWSER_TITLE" => "Y",
						"SET_LAST_MODIFIED" => "N",
						"SET_META_DESCRIPTION" => "Y",
						"SET_META_KEYWORDS" => "Y",
						"SET_STATUS_404" => "N",
						"SET_TITLE" => "Y",
						"SHOW_404" => "N",
						"SORT_BY1" => "ACTIVE_FROM",
						"SORT_BY2" => "SORT",
						"SORT_ORDER1" => "DESC",
						"SORT_ORDER2" => "ASC",
						"STRICT_SECTION_CHECK" => "N"
					)
				);?>
			</div>
		</div>
	</div>
</main>
<div class="modal fade" id="confirmDeleteModal" aria-hidden="true" aria-labelledby="docsForClientLabel" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-body">
				<h3 class="title" id="docsForClientLabel">Уверены, что хотите удалить задачу?</h3>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
				<? if(checkAccess(1)): ?>
					<form class="d-flex justify-content-around" method="post" action="/ajax/tasks/delete_task.php">
						<input type="hidden" name="task_id" value="<?= $arResult["ID"] ?>">
						<input style="width: 150px" class="btn btn-danger me-2" type="submit" value="Да">
						<button style="width: 150px" class="btn btn-secondary" data-bs-dismiss="modal">Нет</button>
					</form>
				<? endif; ?>
			</div>
		</div>
	</div>
</div>
<script src="/local/templates/crm-my-prava/js/steps.js"></script>
<script>
	addEventListener("DOMContentLoaded", () => {
		document.querySelector('[data-step-tab=task]').classList.add('active-tab')
		document.querySelector('[data-step=task]').classList.add('active-step')
	})

	$(function() {
		$('.fake-submit').on('click', function() {
			console.log('fwewefwef')
			$('.save-task').submit()
		})
	})	
</script>

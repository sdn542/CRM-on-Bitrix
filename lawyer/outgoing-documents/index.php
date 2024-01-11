<? require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
IncludeTemplateLangFile(__FILE__);
use Bitrix\Iblock\PropertyEnumerationTable;
$APPLICATION->SetTitle('Почта');
$APPLICATION->SetPageProperty("description", 'Почта');
if(!empty($_POST)){
	$current_url = explode('?', $_SERVER['REQUEST_URI'])[0];
	$get_params_string = http_build_query(array_merge($_GET, $_POST));
	$new_url = $current_url . '?' . $get_params_string;
	header('Location: ' . $new_url);
}
?>
<main class="main">
	<div class="container">
		<?$APPLICATION->IncludeComponent(
			"bitrix:menu",
			"",
			Array(
				"ALLOW_MULTI_SELECT" => "N",
				"CHILD_MENU_TYPE" => "left",
				"DELAY" => "N",
				"MAX_LEVEL" => "1",
				"MENU_CACHE_GET_VARS" => array(""),
				"MENU_CACHE_TIME" => "3600",
				"MENU_CACHE_TYPE" => "N",
				"MENU_CACHE_USE_GROUPS" => "Y",
				"ROOT_MENU_TYPE" => "top",
				"USE_EXT" => "N"
			)
		);?>
		<div class="d-flex gap-4 align-items-center">
			<div class="d-flex justify-content-between align-items-end my-3">
				<form class="d-flex align-items-end gap-2" method="post">
					<div>
						<label class="form-label" for="limit">Количество клиентов</label>
						<select class="form-select" name="limit">
							<option value="20" <?= $_REQUEST["limit"] == 20 ? "selected" : "" ?>>20</option>
							<option value="50" <?= $_REQUEST["limit"] == 50 ? "selected" : "" ?>>50</option>
							<option value="100" <?= $_REQUEST["limit"] == 100 ? "selected" : "" ?>>100</option>
							<option value="999" <?= $_REQUEST["limit"] == 999 ? "selected" : "" ?>>Все</option>
						</select>
					</div>
					<input class="btn btn-secondary mt-2" type="submit" value="Применить">
				</form>
			</div>
			<div class="filter outgoing-filter">
				<button class="filter-btn">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M3 4C3 3.73478 3.10536 3.48043 3.29289 3.29289C3.48043 3.10536 3.73478 3 4 3H20C20.2652 3 20.5196 3.10536 20.7071 3.29289C20.8946 3.48043 21 3.73478 21 4V6.586C20.9999 6.85119 20.8946 7.10551 20.707 7.293L14.293 13.707C14.1055 13.8945 14.0001 14.1488 14 14.414V17L10 21V14.414C9.99994 14.1488 9.89455 13.8945 9.707 13.707L3.293 7.293C3.10545 7.10551 3.00006 6.85119 3 6.586V4Z" stroke="#64748B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
					<svg width="19" height="6" viewBox="0 0 19 6" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M10.4593 5.71849C9.77144 6.09384 8.51713 6.09384 7.82929 5.71849L0.546227 1.74425C-0.182079 1.34683 -0.182079 0.706532 0.546227 0.309108C1.27453 -0.088316 2.44792 -0.088316 3.17622 0.309108L9.12405 3.55474L15.0719 0.309108C15.436 0.110396 15.9216 0 16.4071 0C16.8927 0 17.3782 0.110396 17.7423 0.309108C18.4707 0.706532 18.4707 1.34683 17.7423 1.74425L10.4593 5.71849Z" fill="#64748B"/>
					</svg>
				</button>
				<div class="filter-body">
					<form class="d-flex flex-column gap-2" method="post">
						<select class="form-select" name="client" id="client">
							<option value="">Отправитель</option>
							<?
							$groupCode = "clients";
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
									<option value="<?= $arUser["ID"] ?>" <?= $arUser["ID"] == $_REQUEST["client"] ? "selected" : "" ?>><?= $arUser["LAST_NAME"] . " " . $arUser["NAME"] . (!empty($arUser["SECOND_NAME"]) ? " " . $arUser["SECOND_NAME"] : "") ?></option>
								<?}
							}
							?>
						</select>
						<input class="form-select" name="type" placeholder="Тип задачи" value="<?= $_REQUEST["type"] ?>">
						<select class="form-select" name="type_post_item">
							<option value="">Вид отправки</option>
							<?
							$iblock = CIBlock::GetList([], ['TYPE' => 'content', 'SITE_ID' => SITE_ID, "CODE" => 'tasks'], true)->Fetch();
							if ($iblock) {
								$iblockId = $iblock['ID'];

								$property = \CIBlockProperty::GetList([], ['CODE' => 'TYPE_POST_ITEM', 'IBLOCK_ID' => $iblockId])->Fetch();
								if ($property) {
									$propertyId = $property['ID'];

									$propertyEnum = PropertyEnumerationTable::getList([
										'filter' => ['PROPERTY_ID' => $propertyId],
										'select' => ['ID', 'VALUE']
									])->fetchAll();

									foreach ($propertyEnum as $enum) {
										?>
										<option value="<?= $enum['ID'] ?>" <?= $enum['ID'] == $_REQUEST["type_post_item"] ? "selected" : "" ?>><?= $enum['VALUE'] ?></option>
										<?
									}
								}
							}
							?>
						</select>
						<input class="form-control" type="date" name="date" value="<?= $_REQUEST["date"] ?>">
						<select class="form-select" name="manager" id="manager">
							<option value="">Менеджер</option>
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
									<option value="<?= $arUser["ID"] ?>" <?= $arUser["ID"] == $_REQUEST["manager"] ? "selected" : "" ?>><?= $arUser["LAST_NAME"] . " " . $arUser["NAME"] . (!empty($arUser["SECOND_NAME"]) ? " " . $arUser["SECOND_NAME"] : "") ?></option>
								<?}
							}
							?>
						</select>
						<input class="btn btn-primary" type="submit">
						<a href="<?= parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?>">Сбросить</a>
					</form>
				</div>
			</div>

			<div class="filter incoming-filter filter-hidden">
				<button class="filter-btn">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M3 4C3 3.73478 3.10536 3.48043 3.29289 3.29289C3.48043 3.10536 3.73478 3 4 3H20C20.2652 3 20.5196 3.10536 20.7071 3.29289C20.8946 3.48043 21 3.73478 21 4V6.586C20.9999 6.85119 20.8946 7.10551 20.707 7.293L14.293 13.707C14.1055 13.8945 14.0001 14.1488 14 14.414V17L10 21V14.414C9.99994 14.1488 9.89455 13.8945 9.707 13.707L3.293 7.293C3.10545 7.10551 3.00006 6.85119 3 6.586V4Z" stroke="#64748B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
					<svg width="19" height="6" viewBox="0 0 19 6" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M10.4593 5.71849C9.77144 6.09384 8.51713 6.09384 7.82929 5.71849L0.546227 1.74425C-0.182079 1.34683 -0.182079 0.706532 0.546227 0.309108C1.27453 -0.088316 2.44792 -0.088316 3.17622 0.309108L9.12405 3.55474L15.0719 0.309108C15.436 0.110396 15.9216 0 16.4071 0C16.8927 0 17.3782 0.110396 17.7423 0.309108C18.4707 0.706532 18.4707 1.34683 17.7423 1.74425L10.4593 5.71849Z" fill="#64748B"/>
					</svg>
				</button>
				<div class="filter-body">
					<form class="d-flex flex-column gap-2" method="post">
						<select class="form-select" name="client2" id="client2">
							<option value="">Отправитель</option>
							<?
							$groupCode = "clients";
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
									<option value="<?= $arUser["ID"] ?>" <?= $arUser["ID"] == $_REQUEST["client2"] ? "selected" : "" ?>><?= $arUser["LAST_NAME"] . " " . $arUser["NAME"] . (!empty($arUser["SECOND_NAME"]) ? " " . $arUser["SECOND_NAME"] : "") ?></option>
								<?}
							}
							?>
						</select>
						<input class="form-select" name="name" placeholder="Название" value="<?= $_REQUEST["name"] ?>">
						<input class="form-control" type="date" name="date2" value="<?= $_REQUEST["date2"] ?>">
						<select class="form-select" name="accepter" id="accepter">
							<option value="">Принявший</option>
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
									<option value="<?= $arUser["ID"] ?>" <?= $arUser["ID"] == $_REQUEST["accepter"] ? "selected" : "" ?>><?= $arUser["LAST_NAME"] . " " . $arUser["NAME"] . (!empty($arUser["SECOND_NAME"]) ? " " . $arUser["SECOND_NAME"] : "") ?></option>
								<?}
							}
							?>
						</select>
						<select class="form-select" name="method_receiving">
							<option value="">Способ получения</option>
							<?
							$iblock = CIBlock::GetList([], ['TYPE' => 'content', 'SITE_ID' => SITE_ID, "CODE" => 'incoming-documents'], true)->Fetch();
							if ($iblock) {
								$iblockId = $iblock['ID'];

								$property = \CIBlockProperty::GetList([], ['CODE' => 'METHOD_RECEIVING', 'IBLOCK_ID' => $iblockId])->Fetch();
								if ($property) {
									$propertyId = $property['ID'];

									$propertyEnum = PropertyEnumerationTable::getList([
										'filter' => ['PROPERTY_ID' => $propertyId],
										'select' => ['ID', 'VALUE']
									])->fetchAll();

									foreach ($propertyEnum as $enum) {
										?>
										<option value="<?= $enum['ID'] ?>" <?= $enum['ID'] == $_REQUEST["method_receiving"] ? "selected" : "" ?>><?= $enum['VALUE'] ?></option>
										<?
									}
								}
							}
							?>
						</select>
						<input class="btn btn-primary" type="submit">
						<a href="<?= parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?>">Сбросить</a>
					</form>
				</div>
			</div>
			
		</div>

		<div class="create-client-tabs my-4 mx-auto">
			<h3 data-step-tab="outgoing">Исходящие документы</h3>
			<h3 data-step-tab="incoming">Входящие документы</h3>
		</div>
		<div data-step="outgoing" class="block p-0">
			<?
			global $arFilter;
			$arFilter = array(
				"!PROPERTY_TYPE_POST_ITEM" => false
			);
			if (!empty($_REQUEST["client"])) {
				$arFilter["PROPERTY_CLIENT"] = $_REQUEST["client"];
			}
			if (!empty($_REQUEST["type"])) {
				$arFilter["PROPERTY_TYPE_TASK"] = "%".$_REQUEST["type"]."%";
			}
			if (!empty($_REQUEST["type_post_item"])) {
				$arFilter["PROPERTY_TYPE_POST_ITEM"] = $_REQUEST["type_post_item"];
			}
			if (!empty($_REQUEST["date"])) {
				$arFilter["PROPERTY_DATE_SENDING"] = FormatDate("Y-m-d", strtotime($_REQUEST["date"]));
			}
			if (!empty($_REQUEST["manager"])) {
				$arFilter["PROPERTY_LAWYER"] = $_REQUEST["manager"];
			}
			if (!empty($_REQUEST["sort"]) && !empty($_REQUEST["direct"])) {
				$sort = $_REQUEST["sort"];
				$direct = $_REQUEST["direct"];
			}
			$APPLICATION->IncludeComponent(
				"bitrix:news.list",
				"lawyer-outgoing-documents",
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
					"CHECK_DATES" => "N",
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
					"IBLOCK_ID" => "tasks",
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
					"SET_TITLE" => "N",
					"SHOW_404" => "N",
					"SORT_BY1" => !empty($sort) ? $sort : "ACTIVE_FROM",
					"SORT_BY2" => "SORT",
					"SORT_ORDER1" => !empty($direct) ? $direct : "DESC",
					"SORT_ORDER2" => "ASC",
					"STRICT_SECTION_CHECK" => "N",
					"USER_DATA" => $arUser
				)
			);?>
		</div>
		<div data-step="incoming" class="block p-0" style="background-color: transparent;" >
			<?
			global $arrFilter;
			$arrFilter = array();
			if (!empty($_REQUEST["client2"])) {
				$arrFilter["PROPERTY_SENDER"] = $_REQUEST["client2"];
			}
			if (!empty($_REQUEST["name"])) {
				$arrFilter["NAME"] = "%".$_REQUEST["name"]."%";
			}
			if (!empty($_REQUEST["date2"])) {
				$arrFilter["DATE_ACTIVE_FROM"] = FormatDate("d.m.Y", strtotime($_REQUEST["date2"]));
			}
			if (!empty($_REQUEST["accepter"])) {
				$arrFilter["PROPERTY_ACCEPTER"] = $_REQUEST["accepter"];
			}
			if (!empty($_REQUEST["method_receiving"])) {
				$arrFilter["PROPERTY_METHOD_RECEIVING"] = $_REQUEST["method_receiving"];
			}
			if (!empty($_REQUEST["sort2"]) && !empty($_REQUEST["direct2"])) {
				$sort2 = $_REQUEST["sort2"];
				$direct2 = $_REQUEST["direct2"];
			}
			$APPLICATION->IncludeComponent(
				"bitrix:news.list",
				"lawyer-incoming-documents",
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
					"FILTER_NAME" => "arrFilter",
					"HIDE_LINK_WHEN_NO_DETAIL" => "N",
					"IBLOCK_ID" => "incoming_documents",
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
					"SET_TITLE" => "N",
					"SHOW_404" => "N",
					"SORT_BY1" => !empty($sort2) ? $sort2 : "ACTIVE_FROM",
					"SORT_BY2" => "SORT",
					"SORT_ORDER1" => !empty($direct2) ? $direct2 : "DESC",
					"SORT_ORDER2" => "ASC",
					"STRICT_SECTION_CHECK" => "N"
				)
			);?>
		</div>
	</div>
</main>
<script src="/local/templates/crm-my-prava/js/steps.js"></script>
<script>
	addEventListener("DOMContentLoaded", () => {
		document.querySelector('[data-step-tab=outgoing]').classList.add('active-tab')
		document.querySelector('[data-step=outgoing]').classList.add('active-step')
	})
</script>
<script src="/local/templates/crm-my-prava/js/ajax-form.js"></script>
<script src="/local/templates/crm-my-prava/js/filter.js"></script>
<script>
	$(function() {
		$('#name-search').on('change', function(event) {
			const value = event.target.value
			$.ajax({
				type: "POST",
				url: '/ajax/get_clients.php',
				data: {fio: value},
			}).done(function(response) {
				const result = jQuery.parseJSON(response)
				const values = result.data
				for (key in values) {
					$('#name-select').append(`<option value=${key}>${values[key]}</option>`)
				}
			}).fail(function() {
				console.log('fail');
			});
		})

		function checkFilter() {
			if ($('h3[data-step-tab="outgoing"]').hasClass('active-tab')) {
				console.log('out')
				$('.outgoing-filter').addClass('filter-hidden')
				$('.incoming-filter').removeClass('filter-hidden')
			} else {
				console.log('in')
				$('.outgoing-filter').addClass('filter-hidden')
				$('.incoming-filter').removeClass('filter-hidden')
			}
		}

		$('.create-client-tabs').on('click', function() {
			checkFilter()
		})
	});
</script>
<? require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php'); ?>


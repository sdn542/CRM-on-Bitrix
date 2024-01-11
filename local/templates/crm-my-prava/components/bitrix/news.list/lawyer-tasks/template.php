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
?>
<div class="table-responsive">
	<table class="table main-table">
		<thead class="table-secondary">
		<tr>

			<th scope="col">№</th>
			<th scope="col"><a href="<?= "?" . http_build_query(array_merge($_GET, array("sort" => "PROPERTY_TYPE_TASK", "direct" => ($_REQUEST["sort"] == "PROPERTY_TYPE_TASK" && $_REQUEST["direct"] == "ASC") ? "DESC" : "ASC"))) ?>">Название задачи <?= ($_REQUEST["sort"] == "PROPERTY_TYPE_TASK" && $_REQUEST["direct"] == "ASC") ? "по возрастанию" : "по убыванию" ?></a></th>
			<th scope="col"><a href="<?= "?" . http_build_query(array_merge($_GET, array("sort" => "PROPERTY_CONTRACT.PROPERTY_NUMBER", "direct" => ($_REQUEST["sort"] == "PROPERTY_CONTRACT.PROPERTY_NUMBER" && $_REQUEST["direct"] == "ASC") ? "DESC" : "ASC"))) ?>">№ договора <?= ($_REQUEST["sort"] == "PROPERTY_CONTRACT.PROPERTY_NUMBER" && $_REQUEST["direct"] == "ASC") ? "по возрастанию" : "по убыванию" ?></a></th>
			<th scope="col"><a href="<?= "?" . http_build_query(array_merge($_GET, array("sort" => "PROPERTY_CLIENT", "direct" => ($_REQUEST["sort"] == "PROPERTY_CLIENT" && $_REQUEST["direct"] == "ASC") ? "DESC" : "ASC"))) ?>">ФИО клиента <?= ($_REQUEST["sort"] == "PROPERTY_CLIENT" && $_REQUEST["direct"] == "ASC") ? "по возрастанию" : "по убыванию" ?></a></th>
			<th scope="col">Дата начала</th>
			<th scope="col">Промежуток</th>
			<th scope="col">Крайний срок</th>
			<th scope="col">Вид отправления</th>
			<th scope="col">Статус задачи</th>
			<th scope="col">Исполнитель</th>
			<th scope="col">Постановщик</th>
		</tr>
		</thead>
		<tbody>
			<?foreach($arResult["ITEMS"] as $arItem):?>
				<?
				$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
				$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
				$arClient = CUser::GetByID($arItem["PROPERTIES"]["CLIENT"]["VALUE"])->Fetch();
				$arLawyer = CUser::GetByID($arItem["PROPERTIES"]["LAWYER"]["VALUE"])->Fetch();
				$arCreator = CUser::GetByID($arItem["PROPERTIES"]["CREATOR"]["VALUE"])->Fetch();
				$resContract = CIBlockElement::GetByID($arItem["PROPERTIES"]["CONTRACT"]["VALUE"])->GetNextElement();
				if($resContract){
					$arContract = array(
						"FIELDS" => $resContract->GetFields(),
						"PROPERTIES" => $resContract->GetProperties()
					);
				} else {
					continue;
				}
				?>
				<tr>
					<td>
						<input data-id="<?=$arItem["ID"] ?>" class="form-check-input graph-check me-1" type="checkbox" value="N">
						<?=$arItem["ID"] ?>
					</td>
					<td><a href="<?= $arItem["DETAIL_PAGE_URL"] ?>"><?= $arItem["PROPERTIES"]["TYPE_TASK"]["VALUE"] ?></a></td>
					<td><?= $arContract["PROPERTIES"]["NUMBER"]["VALUE"] ?></td>
					<td><?= $arClient["LAST_NAME"] . " " . $arClient["NAME"] . (!empty($arClient["SECOND_NAME"]) ? " " . $arClient["SECOND_NAME"] : "") ?></td>
					<td><?= $arItem["ACTIVE_FROM"] ?></td>
					<td><?= $arItem["PROPERTIES"]["INTERIM_DATE"]["VALUE"] ?></td>
					<td><?= $arItem["PROPERTIES"]["DEADLINE"]["VALUE"] ?></td>
					<td><?= $arItem["PROPERTIES"]["TYPE_POST_ITEM"]["VALUE_ENUM"] ?></td>
					<td><?= $arItem["PROPERTIES"]["STATUS_TASK"]["VALUE_ENUM"] ?></td>
					<td><?= $arLawyer["LAST_NAME"] . " " . $arLawyer["NAME"] . (!empty($arLawyer["SECOND_NAME"]) ? " " . $arLawyer["SECOND_NAME"] : "") ?></td>
					<td><?= $arCreator["LAST_NAME"] . " " . $arCreator["NAME"] . (!empty($arCreator["SECOND_NAME"]) ? " " . $arCreator["SECOND_NAME"] : "") ?></td>
				</tr>
			<?endforeach;?>
			<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
				<br /><?=$arResult["NAV_STRING"]?>
			<?endif;?>
		</tbody>
	</table>
</div>
<button type="button" class="btn btn-danger mt-3 del-btn">Удалить</button>
<script>
	$(function() {
		$('.del-btn').on('click', function() {
			let arrId = []
			$('.graph-check:checked').each(function() {
				let id = $(this).attr('data-id')
				arrId.push(id)
			})

			console.log(arrId)

			$.ajax({
				type: "POST",
				url: '/ajax/contracts/delete_task.php',
				data: {task_id: arrId},
			}).done(function(response) {
				location.reload()
			}).fail(function() {
				console.log('fail');
			});
		})
	});
</script>

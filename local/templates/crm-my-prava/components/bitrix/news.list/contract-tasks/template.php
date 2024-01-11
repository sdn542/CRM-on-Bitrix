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
				<th scope="col">Название</th>
				<th scope="col">Статус</th>
				<th scope="col">Дата начала</th>
				<th scope="col">Промежуток</th>
				<th scope="col">Крайний срок</th>
				<th scope="col">Исполнитель</th>
				<th scope="col">Постановщик</th>
			</tr>
		</thead>
		<tbody>
			<?foreach($arResult["ITEMS"] as $arItem):?>
				<?
				$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
				$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
				$arUser = CUser::GetByID($arItem["PROPERTIES"]["LAWYER"]["VALUE"])->Fetch();
				?>
				<tr>
					<td><input class="me-2 check-task" data-id="<?= $arItem["ID"] ?>" type="checkbox"><a href="<?= $arItem["DETAIL_PAGE_URL"] ?>"><?= $arItem["PROPERTIES"]["TYPE_TASK"]["VALUE"] ?></a></td>
					<td><?= $arItem["PROPERTIES"]["STATUS_TASK"]["VALUE"] ?></td>
					<td><?= $arItem["ACTIVE_FROM"] ?></td>
					<td><?= $arItem["PROPERTIES"]["INTERIM_DATE"]["VALUE"] ?></td>
					<td><?= $arItem["PROPERTIES"]["DEADLINE"]["VALUE"] ?></td>
					<td><?= $arUser["LAST_NAME"] . " " . $arUser["NAME"] . (!empty($arUser["SECOND_NAME"]) ? " " . $arUser["SECOND_NAME"] : "") ?></td>
					<td><?= $arUser["LAST_NAME"] . " " . $arUser["NAME"] . (!empty($arUser["SECOND_NAME"]) ? " " . $arUser["SECOND_NAME"] : "") ?></td>
				</tr>
			<?endforeach;?>
			<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
				<br /><?=$arResult["NAV_STRING"]?>
			<?endif;?>
		</tbody>
	</table>
	<? if(checkAccess(2)): ?>
	<a class="btn btn-primary" href="/lawyer/tasks/create/">Создать задачу</a>
	<? endif; ?>
	<? if(checkAccess(1)): ?>
		<a class="btn btn-danger del-btn">Удалить задачу</a>
	<? endif; ?>
</div>

<script>
	 $(function() {
		$('.del-btn').on('click', function() {
			let arrId = []
			$('.check-task:checked').each(function() {
				let id = $(this).attr('data-id')
				arrId.push(id)
			})

			$.ajax({
				type: "POST",
				url: '/ajax/contracts/delete_task.php',
				data: {task_id: arrId }
			}).done(function(response) {
				console.log(response)
				location.reload()
			}).fail(function() {
				console.log('fail');
			});
		})
	 })
</script>
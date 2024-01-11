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
if(!empty($arResult["ITEMS"])){
?>
<div class="table-responsive">
	<table class="table main-table">
		<thead>
			<tr class="table-secondary">
				<th scope="col">Адресат</th>
				<th scope="col">Адрес</th>
				<th scope="col">Track-номер</th>
				<th scope="col">Документ</th>
				<th scope="col">Ответ</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?foreach($arResult["ITEMS"] as $arItem):?>
				<?
				$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
				$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
				if(!empty($arItem["PROPERTIES"]["CREDITOR"]["VALUE"])){
					$resCreditor = CIBlockElement::GetByID($arItem["PROPERTIES"]["CREDITOR"]["VALUE"])->GetNextElement();
					if($resCreditor) {
						$arCreditor = array(
							"FIELDS" => $resCreditor->GetFields(),
							"PROPERTIES" => $resCreditor->GetProperties()
						);
					}
				}
				?>
				<tr>
					<td><?= $arCreditor["FIELDS"]["NAME"] ?></td>
					<td><?= $arCreditor["PROPERTIES"]["ADDRESS"]["VALUE"] ?></td>
					<td>
						<form enctype="multipart/form-data" method="post" action="/ajax/tasks/addresses/save_track.php">
							<input class="form-control" type="text" name="task_id" value="<?= $arParams["TASK_ID"] ?>" hidden>
							<input class="form-control" type="text" name="addressed_id" value="<?= $arItem["ID"] ?>" hidden>
							<input class="form-control" type="text" name="property_code" value="TRACK_NUMBER" hidden>
							<input class="form-control" type="text" name="track" value="<?= $arItem["PROPERTIES"]["TRACK_NUMBER"]["VALUE"] ?>" <?= checkAccess(1) ? "" : "disabled" ?>>
							<input class="btn btn-primary mt-1" type="submit">
						</form>
					</td>
					<td>
						<?if(!empty($arItem["PROPERTIES"]["DOC"]["VALUE"])):?>
							<div class="d-flex align-items-center">
								<a href="<?= CFile::GetPath($arItem["PROPERTIES"]["DOC"]["VALUE"]) ?>" download><?= CFile::GetFileArray($arItem["PROPERTIES"]["DOC"]["VALUE"])["ORIGINAL_NAME"] ?></a>
								<? if(checkAccess(2)): ?>
									<form action="/ajax/tasks/addresses/delete_file.php" method="post">
										<input class="form-control" type="text" name="task_id" value="<?= $arParams["TASK_ID"] ?>" hidden>
										<input class="form-control" type="text" name="addressed_id" value="<?= $arItem["ID"] ?>" hidden>
										<input class="form-control" type="text" name="file_id" value="<?= $arItem["PROPERTIES"]["DOC"]["VALUE"] ?>" hidden>
										<input class="form-control" type="text" name="property_code" value="DOC" hidden>
										<input class="btn p-0 ms-1" type="submit" value="x">
									</form>
							</div>
							<? endif; ?>
						<? else: ?>
							<form enctype="multipart/form-data" method="post" action="/ajax/tasks/addresses/add_file.php">
								<input class="form-control" type="text" name="task_id" value="<?= $arParams["TASK_ID"] ?>" hidden>
								<input class="form-control" type="text" name="addressed_id" value="<?= $arItem["ID"] ?>" hidden>
								<input class="form-control" type="text" name="property_code" value="DOC" hidden>
								<input class="form-control" style="width: 144px" type="file" name="file">
								<input class="btn btn-primary mt-1" type="submit">
							</form>
						<? endif; ?>
					</td>
					<td>
						<?if(!empty($arItem["PROPERTIES"]["RESPONSE"]["VALUE"])):?>
							<div class="d-flex align-items-center">
								<a href="<?= CFile::GetPath($arItem["PROPERTIES"]["RESPONSE"]["VALUE"]) ?>" download><?= CFile::GetFileArray($arItem["PROPERTIES"]["RESPONSE"]["VALUE"])["ORIGINAL_NAME"] ?></a>
								<? if(checkAccess(2)): ?>
								<form action="/ajax/tasks/addresses/delete_file.php" method="post">
									<input class="form-control" type="text" name="task_id" value="<?= $arParams["TASK_ID"] ?>" hidden>
									<input class="form-control" type="text" name="addressed_id" value="<?= $arItem["ID"] ?>" hidden>
									<input class="form-control" type="text" name="file_id" value="<?= $arItem["PROPERTIES"]["RESPONSE"]["VALUE"] ?>" hidden>
									<input class="form-control" type="text" name="property_code" value="RESPONSE" hidden>
									<input class="btn p-0 ms-1" type="submit" value="x">
								</form>
							</div>
							<? endif; ?>
						<? else: ?>
							<form enctype="multipart/form-data" method="post" action="/ajax/tasks/addresses/add_file.php">
								<input class="form-control" type="text" name="task_id" value="<?= $arParams["TASK_ID"] ?>" hidden>
								<input class="form-control" type="text" name="addressed_id" value="<?= $arItem["ID"] ?>" hidden>
								<input class="form-control" type="text" name="property_code" value="RESPONSE" hidden>
								<input class="form-control" style="width: 144px" type="file" name="file">
								<input class="btn btn-primary mt-1" type="submit">
							</form>
						<? endif; ?>
					</td>
					<td>
						<? if(checkAccess(2)): ?>
							<form method="post" action="/ajax/tasks/delete_creditor.php">
								<input class="form-control" type="text" name="task_id" value="<?= $arParams["TASK_ID"] ?>" hidden>
								<input class="form-control" type="hidden" name="creditor_id[]" value="<?= $arItem["ID"] ?>">
								<input class="btn btn-danger" type="submit" value="Удалить">
						</form>
						<? endif; ?>
					</td>
				</tr>
			<?endforeach;?>
			<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
				<br /><?=$arResult["NAV_STRING"]?>
			<?endif;?>
		</tbody>
	</table>
</div>
<?}?>
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
use Bitrix\Iblock\IblockTable;
use Bitrix\Iblock\ElementTable;
$this->setFrameMode(true);
$iblockTasks = IblockTable::getList([
	'select' => ['ID'],
	'filter' => ['CODE' => "tasks"],
])->fetch();
$iblockCreditors = IblockTable::getList([
	'select' => ['ID'],
	'filter' => ['CODE' => "creditors"],
])->fetch();
?>
<div class="d-flex gap-2 justify-content-end">
	<button class="btn btn-primary fake-sub-btn d-none">Сохранить</button>
	<button class="btn btn-secondary create-doc-btn">Создать входящий документ</button>
</div>
<div class="table-responsive">
	<table class="table main-table">
		<thead class="table-secondary">
		<tr>
			<th scope="col">№</th>
			<th scope="col"><a href="<?= "?" . http_build_query(array_merge($_GET, array("sort2" => "PROPERTY_CONTRACT.PROPERTY_NUMBER", "direct2" => ($_REQUEST["sort2"] == "PROPERTY_CONTRACT.PROPERTY_NUMBER" && $_REQUEST["direct2"] == "ASC") ? "DESC" : "ASC"))) ?>">№ договора <?= ($_REQUEST["sort2"] == "PROPERTY_CONTRACT.PROPERTY_NUMBER" && $_REQUEST["direct2"] == "ASC") ? "по возрастанию" : "по убыванию" ?></a></th>
			<th scope="col"><a href="<?= "?" . http_build_query(array_merge($_GET, array("sort2" => "PROPERTY_SENDER", "direct2" => ($_REQUEST["sort2"] == "PROPERTY_SENDER" && $_REQUEST["direct2"] == "ASC") ? "DESC" : "ASC"))) ?>">Отправитель <?= ($_REQUEST["sort2"] == "PROPERTY_SENDER" && $_REQUEST["direct2"] == "ASC") ? "по возрастанию" : "по убыванию" ?></a></th>
			<th scope="col">Название</th>
			<th scope="col"><a href="<?= "?" . http_build_query(array_merge($_GET, array("sort2" => "ACTIVE_FROM", "direct2" => ($_REQUEST["sort2"] == "ACTIVE_FROM" && $_REQUEST["direct2"] == "ASC") ? "DESC" : "ASC"))) ?>">Дата поступления <?= ($_REQUEST["sort2"] == "ACTIVE_FROM" && $_REQUEST["direct2"] == "ASC") ? "по возрастанию" : "по убыванию" ?></a></th>
			<th scope="col"><a href="<?= "?" . http_build_query(array_merge($_GET, array("sort2" => "PROPERTY_ACCEPTER", "direct2" => ($_REQUEST["sort2"] == "PROPERTY_ACCEPTER" && $_REQUEST["direct2"] == "ASC") ? "DESC" : "ASC"))) ?>">Принявший <?= ($_REQUEST["sort2"] == "PROPERTY_ACCEPTER" && $_REQUEST["direct2"] == "ASC") ? "по возрастанию" : "по убыванию" ?></a></th>
			<th scope="col">Способ получения</th>
			<th scope="col">Резолюция</th>
			<th scope="col">Дата резолюции</th>
			<th scope="col">Файл</th>
		</tr>
		</thead>
		<tbody>
		<? if(checkAccess(2)): ?>
		<tr class="create-row d-none">
			<form method="post" enctype="multipart/form-data" action="/ajax/incoming-documents/add.php">
				<td></td>
				<td>
					<select class="form-select" name="PROPS[CONTRACT]" required>
						<?
						$arFilter = array(
							"IBLOCK_CODE" => "contracts",
							"IBLOCK_TYPE" => "content",
						);
						$arSelect = array("ID", "NAME");
						$rsElements = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
						while ($arElement = $rsElements->Fetch()) {?>
							<option value="<?= $arElement["ID"] ?>"><?= $arElement["NAME"] ?></option>
						<?}
						?>
					</select>
				</td>
				<td>
					<select class="form-select" name="PROPS[SENDER]" required>
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
				</td>
				<td><input style="width: 200px" class="form-control" name="NAME" type="text"></td>
				<td></td>
				<td>
					<select class="form-select" name="PROPS[ACCEPTER]" required>
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
				</td>
				<td>
					<select class="form-select" name="PROPS[METHOD_RECEIVING]">
						<?
						$property_enums = CIBlockPropertyEnum::GetList(Array("ID"=>"ASC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$arResult["ID"], "CODE" => "METHOD_RECEIVING"));
						while($enum_fields = $property_enums->GetNext())
						{?>
							<option
									class="form-check-input"
									type="radio"
									id="<?= $enum_fields["ID"] ?>"
									value="<?= $enum_fields["ID"] ?>"
									style="float: right"
							><?= $enum_fields["VALUE"] ?></option>
						<?} ?>
					</select>
				</td>
				<td><textarea class="form-control" name="PROPS[RESOLUTION]"></textarea></td>
				<td><input class="form-control" name="PROPS[DATE_RESOLUTION]" type="date"></td>
				<td><input style="width: 300px" class="form-control" type="file" name="PROPS[FILE]"></td>
				<input class="create-doc-sub" type="submit">
			</form>
		</tr>
		<? endif; ?>
			<?foreach($arResult["ITEMS"] as $arItem):?>
				<?
				$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
				$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
				$arSender = CUser::GetByID($arItem["PROPERTIES"]["SENDER"]["VALUE"])->Fetch();
				$arAccepter = CUser::GetByID($arItem["PROPERTIES"]["ACCEPTER"]["VALUE"])->Fetch();
				if(!empty($arItem["PROPERTIES"]["CONTRACT"]["VALUE"])) {
					$resContract = CIBlockElement::GetByID($arItem["PROPERTIES"]["CONTRACT"]["VALUE"])->GetNextElement();
					if($resContract){
						$arContract = array(
							"FIELDS" => $resContract->GetFields(),
							"PROPERTIES" => $resContract->GetProperties()
						);
					}
				}
				?>
				<tr>
					<td><?= $arItem['ID'] ?></td>
					<td>
						<form class="form-ajax" method="post" action="/ajax/incoming-documents/update.php">
							<input type="text" name="doc_id" value="<?= $arItem["ID"] ?>" hidden>
							<select class="form-select ajax" name="CONTRACT" required <?= checkAccess(2) ? "" : "disabled" ?>>
								<?
								$arFilter = array(
									"IBLOCK_CODE" => "contracts",
									"IBLOCK_TYPE" => "content",
								);
								$arSelect = array("ID", "NAME");
								$rsElements = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
								while ($arElement = $rsElements->Fetch()) {?>
									<option value="<?= $arElement["ID"] ?>" <?= $arContract['FIELDS']["ID"] === $arElement["ID"] ? "selected" : "" ?>><?= $arElement["NAME"] ?></option>
								<?}
								?>
							</select>
							<input class="btn btn-secondary mt-2" type="submit" hidden>
						</form>
					</td>
					<td>
						<form class="form-ajax" method="post" action="/ajax/incoming-documents/update.php">
							<input type="text" name="doc_id" value="<?= $arItem["ID"] ?>" hidden>
							<select class="form-select ajax" name="SENDER" required <?= checkAccess(2) ? "" : "disabled" ?>>
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
										<option value="<?= $arUser["ID"] ?>" <?= $arSender["ID"] === $arUser["ID"] ? "selected" : "" ?>><?= $arUser["LAST_NAME"] . " " . $arUser["NAME"] . (!empty($arUser["SECOND_NAME"]) ? " " . $arUser["SECOND_NAME"] : "") ?></option>
									<?}
								}
								?>
							</select>
							<input class="btn btn-secondary mt-2" type="submit" hidden>
						</form>
					</td>
					<td>
						<form class="form-ajax" method="post" action="/ajax/incoming-documents/update_name.php">
							<input type="text" name="doc_id" value="<?= $arItem["ID"] ?>" hidden>
							<input style="width: 200px" class="form-control ajax" name="NAME" type="text" value="<?= $arItem["NAME"] ?>" <?= checkAccess(2) ? "" : "disabled" ?>>
							<input class="btn btn-secondary mt-2" type="submit" hidden>
						</form>
					</td>
					<td><?= $arItem["ACTIVE_FROM"] ?></td>
					<td>
						<form class="form-ajax" method="post" action="/ajax/incoming-documents/update.php">
							<input type="text" name="doc_id" value="<?= $arItem["ID"] ?>" hidden>
							<select class="form-select ajax" name="ACCEPTER" required <?= checkAccess(2) ? "" : "disabled" ?>>
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
										<option value="<?= $arUser["ID"] ?>" <?= $arAccepter["ID"] === $arUser["ID"] ? "selected" : "" ?>><?= $arUser["LAST_NAME"] . " " . $arUser["NAME"] . (!empty($arUser["SECOND_NAME"]) ? " " . $arUser["SECOND_NAME"] : "") ?></option>
									<?}
								}
								?>
							</select>
							<input class="btn btn-secondary mt-2" type="submit" hidden>
						</form>
					<td>
						<form class="form-ajax" method="post" action="/ajax/incoming-documents/update.php">
							<input type="text" name="doc_id" value="<?= $arItem["ID"] ?>" hidden>
							<select class="form-select ajax" name="METHOD_RECEIVING" <?= checkAccess(2) ? "" : "disabled" ?>>
								<?
								$property_enums = CIBlockPropertyEnum::GetList(Array("ID"=>"ASC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$arItem["IBLOCK_ID"], "CODE" => "METHOD_RECEIVING"));
								while($enum_fields = $property_enums->GetNext())
								{?>
									<option
											class="form-check-input"
											type="radio"
											id="<?= $enum_fields["ID"] ?>"
											value="<?= $enum_fields["ID"] ?>"
										<?= $arItem["PROPERTIES"]["METHOD_RECEIVING"]["VALUE_ENUM_ID"] === $enum_fields["ID"] ? "selected" : "" ?>
											style="float: right"
									><?= $enum_fields["VALUE"] ?></option>
								<?} ?>
							</select>
							<input class="btn btn-secondary mt-2" type="submit" hidden>
						</form>
					</td>
					<td>
						<form class="form-ajax" method="post" action="/ajax/incoming-documents/update.php">
							<input type="text" name="doc_id" value="<?= $arItem["ID"] ?>" hidden>
							<textarea class="form-control ajax" name="RESOLUTION" <?= checkAccess(1) ? "" : "disabled" ?>>
								<?= !empty($arItem["PROPERTIES"]["RESOLUTION"]["VALUE"]) ? $arItem["PROPERTIES"]["RESOLUTION"]["VALUE"]["TEXT"] : "" ?>
							</textarea>
							<input class="btn btn-secondary mt-2" type="submit" hidden>
						</form>
					</td>
					<td>
						<form class="form-ajax" method="post" action="/ajax/incoming-documents/update.php">
							<input type="text" name="doc_id" value="<?= $arItem["ID"] ?>" hidden>
							<input class="form-control ajax" type="date" name="DATE_RESOLUTION" <?= checkAccess(1) ? "" : "disabled" ?> value="<?= date("Y-m-d", strtotime($arItem["PROPERTIES"]["DATE_RESOLUTION"]["VALUE"])) ?>">
							<input class="btn btn-secondary mt-2" type="submit" hidden>
						</form>
					</td>
					<td>
						<? if(!empty($arItem["PROPERTIES"]["FILE"]["VALUE"])): ?>
							<div class="d-flex align-items-center">
								<a href="<?= CFile::GetPath($arItem["PROPERTIES"]["FILE"]["VALUE"]) ?>" download><?= CFile::GetFileArray($arItem["PROPERTIES"]["FILE"]["VALUE"])["ORIGINAL_NAME"] ?></a>
								<? if(checkAccess(2)): ?>
									<form action="/ajax/incoming-documents/delete_file.php" method="post">
										<input type="text" name="doc_id" value="<?= $arItem["ID"] ?>" hidden>
										<input class="form-control" type="text" name="file_id" value="<?= $arItem["PROPERTIES"]["FILE"]["VALUE"] ?>" hidden>
										<input class="form-control" type="text" name="property_code" value="FILE" hidden>
										<input class="btn p-0 mx-2" type="submit" value="x">
									</form>
								<? endif; ?>
							</div>
						<? else: ?>
							<? if(checkAccess(2)): ?>
								<form enctype="multipart/form-data" method="post" action="/ajax/incoming-documents/add_file.php">
									<input type="text" name="doc_id" value="<?= $arItem["ID"] ?>" hidden>
									<input class="form-control" type="text" name="property_code" value="FILE" hidden>
									<input class="form-control" type="file" name="file">
									<input class="btn btn-secondary mt-2" type="submit" value="Прикрепить">
								</form>
							<? endif; ?>
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
<script>
	$(function() {
		$('.create-doc-btn').on('click', function() {
			$('.create-row').toggleClass("d-none")
			if ($('.create-row').hasClass("d-none")) {
				$('.create-doc-btn').html('Создать входящий документ')
				$('.fake-sub-btn').addClass('d-none')
			} else {
				$('.create-doc-btn').html('Отменить')
				$('.fake-sub-btn').removeClass('d-none')
				$('.fake-sub-btn').on('click', function() {
					$('.create-doc-sub').trigger('click')
				})
			}
			
		})
	});
</script>

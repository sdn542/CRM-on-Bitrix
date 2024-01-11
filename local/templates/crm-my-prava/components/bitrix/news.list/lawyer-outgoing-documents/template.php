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
<div class="table-responsive">
	<table class="table main-table">
		<thead class="table-secondary">
		<tr>
			<th scope="col">№</th>
			<th scope="col"><a href="<?= "?" . http_build_query(array_merge($_GET, array("sort" => "PROPERTY_CLIENT", "direct" => ($_REQUEST["sort"] == "PROPERTY_CLIENT" && $_REQUEST["direct"] == "ASC") ? "DESC" : "ASC"))) ?>">Отправитель <?= ($_REQUEST["sort"] == "PROPERTY_CLIENT" && $_REQUEST["direct"] == "ASC") ? "по возрастанию" : "по убыванию" ?></a></th>
			<th scope="col">Получатель</th>
			<th scope="col">Наименование</th>
			<th scope="col">Вид отправки</th>
			<th scope="col"><a href="<?= "?" . http_build_query(array_merge($_GET, array("sort" => "PROPERTY_DATE_SENDING", "direct" => ($_REQUEST["sort"] == "PROPERTY_DATE_SENDING" && $_REQUEST["direct"] == "ASC") ? "DESC" : "ASC"))) ?>">Дата отправки <?= ($_REQUEST["sort"] == "PROPERTY_DATE_SENDING" && $_REQUEST["direct"] == "ASC") ? "по возрастанию" : "по убыванию" ?></a></th>
			<th scope="col">Track-номер</th>
			<th scope="col">Чек об оплате</th>
			<th scope="col">Менеджер</th>
			<th scope="col">Статус задачи</th>
			<th scope="col">Статус почты</th>
		</tr>
		</thead>
		<tbody>
			<?foreach($arResult["ITEMS"] as $arItem):?>
				<?
				$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
				$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
				$arAddressed = array();
				foreach ($arItem["PROPERTIES"]["ADDRESSEES"]["VALUE"] as $address){
					if(!empty($address))
					{
						$resAddress = CIBlockElement::GetByID($address)->GetNextElement();
						if($resAddress)
						{
							$arAddressed[] = array(
								"FIELDS" => $resAddress->GetFields(),
								"PROPERTIES" => $resAddress->GetProperties()
							);
						}
					}
				}
				$arSender = CUser::GetByID($arItem["PROPERTIES"]["CLIENT"]["VALUE"])->Fetch();
				$arManager = CUser::GetByID($arItem["PROPERTIES"]["LAWYER"]["VALUE"])->Fetch();
				?>
				<tr>
					<td><?= $arItem['ID'] ?></td>
					<td><a href="<?= $arItem["DETAIL_PAGE_URL"] ?>"><?= $arSender["LAST_NAME"] . " " . $arSender["NAME"] . (!empty($arSender["SECOND_NAME"]) ? " " . $arSender["SECOND_NAME"] : "") ?></a></td>
					<td>
						<?if(!empty($arAddressed)):?>
							<button data-expand="<?= $arItem['ID'] ?>" class="trigger">
								<span>Развернуть</span>
								<svg width="10" height="6" viewBox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg">
									<g clip-path="url(#clip0_4794_2278)">
									<path d="M8.29289 0.292893C8.68342 -0.097631 9.31658 -0.097631 9.70711 0.292893C10.0976 0.683418 10.0976 1.31658 9.70711 1.70711L5.70711 5.70711C5.31658 6.09763 4.68342 6.09763 4.29289 5.70711L0.292893 1.70711C-0.097631 1.31658 -0.097631 0.683418 0.292893 0.292893C0.683418 -0.097631 1.31658 -0.097631 1.70711 0.292893L5 3.58579L8.29289 0.292893Z" fill="#64748B"/>
									</g>
									<defs>
									<clipPath id="clip0_4794_2278">
									<rect width="10" height="6" fill="white"/>
									</clipPath>
									</defs>
								</svg>
 
							</button>
						<?endif?>
					</td>
					<td><?= $arItem["PROPERTIES"]["TYPE_TASK"]["VALUE"] ?></td>
					<td><?= $arItem["PROPERTIES"]["TYPE_POST_ITEM"]["VALUE"] ?></td>
					<td><?= $arItem["PROPERTIES"]["DATE_SENDING"]["VALUE"] ?></td>
					<td>
						<?if(!empty($arAddressed)):?>
							<button data-expand="<?= $arItem['ID'] ?>" class="trigger">
								<span>Развернуть</span>
								<svg width="10" height="6" viewBox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg">
									<g clip-path="url(#clip0_4794_2278)">
									<path d="M8.29289 0.292893C8.68342 -0.097631 9.31658 -0.097631 9.70711 0.292893C10.0976 0.683418 10.0976 1.31658 9.70711 1.70711L5.70711 5.70711C5.31658 6.09763 4.68342 6.09763 4.29289 5.70711L0.292893 1.70711C-0.097631 1.31658 -0.097631 0.683418 0.292893 0.292893C0.683418 -0.097631 1.31658 -0.097631 1.70711 0.292893L5 3.58579L8.29289 0.292893Z" fill="#64748B"/>
									</g>
									<defs>
									<clipPath id="clip0_4794_2278">
									<rect width="10" height="6" fill="white"/>
									</clipPath>
									</defs>
								</svg>

							</button>
						<?endif?>
					</td>
					<td>
						<?if(!empty($arAddressed)):?>
							<button data-expand="<?= $arItem['ID'] ?>" class="trigger">
								<span>Развернуть</span>
								<svg width="10" height="6" viewBox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg">
									<g clip-path="url(#clip0_4794_2278)">
									<path d="M8.29289 0.292893C8.68342 -0.097631 9.31658 -0.097631 9.70711 0.292893C10.0976 0.683418 10.0976 1.31658 9.70711 1.70711L5.70711 5.70711C5.31658 6.09763 4.68342 6.09763 4.29289 5.70711L0.292893 1.70711C-0.097631 1.31658 -0.097631 0.683418 0.292893 0.292893C0.683418 -0.097631 1.31658 -0.097631 1.70711 0.292893L5 3.58579L8.29289 0.292893Z" fill="#64748B"/>
									</g>
									<defs>
									<clipPath id="clip0_4794_2278">
									<rect width="10" height="6" fill="white"/>
									</clipPath>
									</defs>
								</svg>
 
							</button>
						<?endif?>
					</td>
					<td><?= $arManager["LAST_NAME"] . " " . $arManager["NAME"] . (!empty($arManager["SECOND_NAME"]) ? " " . $arManager["SECOND_NAME"] : "") ?></td>
					<td>
						<form class="form-ajax" method="post" action="/ajax/tasks/update_status.php">
							<input type="text" name="task_id" value="<?= $arItem["ID"] ?>" hidden>
							<select class="form-select ajax" style="width: 150px" name="STATUS_TASK">
								<?
									$property_enums = CIBlockPropertyEnum::GetList(Array("ID"=>"ASC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$arItem["IBLOCK_ID"], "CODE" => "STATUS_TASK"));
									while($enum_fields = $property_enums->GetNext())
								{?>
									<option value="<?= $enum_fields["ID"] ?>" id="<?= $enum_fields["ID"] ?>" 	<?= $arItem["PROPERTIES"]["STATUS_TASK"]["VALUE_ENUM_ID"] === $enum_fields["ID"] ? "selected" : "" ?> ><?= $enum_fields["VALUE"] ?></option>
										
								<?} ?>
							</select>	
							<input class="btn btn-secondary mt-2" type="submit" value="Применить" hidden>
						</form>
					</td>
					<td>
						<form class="form-ajax" method="post" action="/ajax/tasks/update_status.php">
							<input type="text" name="task_id" value="<?= $arItem["ID"] ?>" hidden>
							<select class="form-select ajax" style="width: 150px" name="STATUS_POST">
								<?
								$property_enums = CIBlockPropertyEnum::GetList(Array("ID"=>"ASC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$arResult["IBLOCK_ID"], "CODE" => "STATUS_POST"));
								while($enum_fields = $property_enums->GetNext())
								{?>
									<option value="<?= $enum_fields["ID"] ?>" id="<?= $enum_fields["ID"] ?>" 	<?= $arItem["PROPERTIES"]["STATUS_POST"]["VALUE_ENUM_ID"] === $enum_fields["ID"] ? "selected" : "" ?> ><?= $enum_fields["VALUE"] ?></option>
								<?} ?>
							</div>
							<input class="btn btn-secondary mt-2" type="submit" hidden>
						</form>
					</td>
				</tr>
				<? foreach ($arAddressed as $address):?>
					<tr data-expand-row=<?= $arItem['ID'] ?> class="d-none">
						<td></td>
						<td></td>
						<td>
							<?if(!empty($address["PROPERTIES"]["CREDITOR"]["VALUE"])):
								$arCreditor = CIBlockElement::GetByID($address["PROPERTIES"]["CREDITOR"]["VALUE"])->GetNextElement();
								if($arCreditor)
								{
									$arCreditor = $arCreditor->GetFields();
								}?>
								<p><?= $arCreditor["NAME"] ?></p>
							<? endif; ?>
						</td>
						<td></td>
						<td></td>
						<td></td>
						<td><p><?= $address["PROPERTIES"]["TRACK_NUMBER"]["VALUE"] ?></p></td>
						<td>
							<? if (!empty($address["PROPERTIES"]["FILE"]["VALUE"])): ?>
								<div class="d-flex align-items-center">
									<a href="<?= CFile::GetPath($address["PROPERTIES"]["FILE"]["VALUE"]) ?>"
										download><?= CFile::GetFileArray($address["PROPERTIES"]["FILE"]["VALUE"])["ORIGINAL_NAME"] ?></a>
									<? if (checkAccess(2)): ?>
										<form action="/ajax/outgoing-documents/delete_file.php" method="post">
											<input type="text" name="doc_id" value="<?= $address["FIELDS"]["ID"] ?>" hidden>
											<input class="form-control" type="text" name="file_id"
														value="<?= $address["PROPERTIES"]["FILE"]["VALUE"] ?>" hidden>
											<input class="form-control" type="text" name="property_code" value="FILE"
														hidden>
											<input class="btn p-0 mx-2" type="submit" value="x">
										</form>
									<? endif; ?>
								</div>
							<? else: ?>
								<? if (checkAccess(2)): ?>
									<button class="btn btn-secondary" data-bs-target="#check-<?= $address["FIELDS"]["ID"] ?>" data-bs-toggle="modal">Прикрепить</button>
									<div class="modal fade" id="check-<?= $address["FIELDS"]["ID"] ?>" aria-hidden="true" aria-labelledby="docsForClientLabel" tabindex="-1">
										<div class="modal-dialog modal-dialog-centered">
											<div class="modal-content">
												<div class="modal-body">
													<h3 class="title" id="docsForClientLabel">Загрузить документ</h3>
													<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
													<form enctype="multipart/form-data" method="post" action="/ajax/outgoing-documents/add_file.php">
														<input type="text" name="doc_id" value="<?= $address["FIELDS"]["ID"] ?>" hidden>
														<input class="form-control" type="text" name="property_code" value="FILE" hidden>
														<input class="form-control" type="file" name="file">
														<input class="btn btn-secondary mt-2" type="submit" value="Прикрепить">
													</form>
												</div>
											</div>
										</div>
									</div>
								<? endif; ?>
							<? endif; ?>
						</td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
				<? endforeach; ?>
			<?endforeach;?>
		</tbody>
	</table>
</div>
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?>
<?endif;?>

<script>
	$(function() {
		var buttons = $('button[data-expand]')
		var expandConainers = $('tr[data-expand-row]')

		buttons.each(function() {
			$(this).on('click', function() {
				var id = $(this).attr('data-expand')
				$(this).toggleClass('trigger-active')

				if($(this).hasClass('trigger-active')) {
					$(this).find('span').html('Скрыть')
				} else {
					$(this).find('span').html('Развернуть')
				}

				expandConainers.each(function() {
					if ($(this).attr('data-expand-row') == id) {
						$(this).toggleClass('d-none')
					}
				})
			})
		})
	})
</script>
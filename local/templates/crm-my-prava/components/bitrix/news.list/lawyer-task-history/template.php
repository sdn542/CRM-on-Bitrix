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
?>
<div class="table-responsive">
	<table class="table main-table">
		<thead class="table-secondary">
		<tr>
			<th scope="col">Время</th>
			<th scope="col">Автор изменений</th>
			<th scope="col">Что изменилось</th>
			<th scope="col">Было</th>
			<th scope="col">Стало</th>
		</tr>
		</thead>
		<tbody>
			<?foreach($arResult["ITEMS"] as $arItem):?>
				<?
				$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
				$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
				$arUser = CUser::GetByID($arItem["PROPERTIES"]["USER"]["VALUE"])->Fetch();
				?>
				<tr>
					<td><?= $arItem['PROPERTIES']["DATETIME"]["VALUE"] ?></td>
					<td><?= $arUser["LAST_NAME"] . " " . $arUser["NAME"] . (!empty($arUser["SECOND_NAME"]) ? " " . $arUser["SECOND_NAME"] : "") ?></td>
					<td><?= $arItem['PROPERTIES']["CHANGED"]["VALUE"] ?></td>
					<td><?= $arItem['PROPERTIES']["BEFORE"]["VALUE"] ?></td>
					<td><?= $arItem['PROPERTIES']["AFTER"]["VALUE"] ?></td>
				</tr>
			<?endforeach;?>
			<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
				<br /><?=$arResult["NAV_STRING"]?>
			<?endif;?>
		</tbody>
	</table>
</div>

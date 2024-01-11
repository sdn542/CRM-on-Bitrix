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
<h2 class="title"><?= GetMessage("LINKED_CONTRACTS") ?></h2>
<div class="table-responsive">
	<table class="table main-table">
		<tr class="table-secondary">
			<th scope="col"><?= GetMessage("LINKED_CONTRACTS_TABLE_NUMBER") ?></th>
			<th scope="col"><?= GetMessage("LINKED_CONTRACTS_TABLE_DATE") ?></th>
			<th scope="col"><?= GetMessage("LINKED_CONTRACTS_TABLE_LAWYER") ?></th>
			<th scope="col"><?= GetMessage("LINKED_CONTRACTS_TABLE_PERCENT") ?></th>
			<th scope="col"><?= GetMessage("LINKED_CONTRACTS_TABLE_SUM") ?></th>
			<th scope="col"><?= GetMessage("LINKED_CONTRACTS_TABLE_REST_PAYMENT") ?></th>
			<th scope="col"><?= GetMessage("LINKED_CONTRACTS_TABLE_STATUS") ?></th>
		</tr>
		<?foreach($arResult["ITEMS"] as $arItem):?>
			<?
			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
			$totalTasks = 0;
			$completedTasks = 0;
			$res = CIBlockElement::GetList(array(), array("IBLOCK_CODE"=>"tasks", "PROPERTY_CONTRACT" => $arItem["ID"]), false, false, array("ID", "IBLOCK_ID", "PROPERTY_STATUS_TASK"));
			while($ob = $res->GetNext()) {
				$totalTasks++;
				if($ob["PROPERTY_STATUS_TASK_VALUE"] == "Выполнено")
				{
					$completedTasks++;
				}
			}
			$completionPercentage = ($totalTasks > 0) ? ($completedTasks / $totalTasks) * 100 : 0;
			$ostatok = (double)$arItem["PROPERTIES"]["CONTRACT_AMOUNT"]["VALUE"]-(double)$arItem["PROPERTIES"]["ADVANCE_PAYMENT"]["VALUE"];
			foreach ($arItem["PROPERTIES"]["PAYMENT"]["VALUE"] as $payment){
				$resPayment = CIBlockElement::GetByID($payment)->GetNextElement();
				if($resPayment)
				{
					$arPayment = array(
						"FIELDS" => $resPayment->GetFields(),
						"PROPERTIES" => $resPayment->GetProperties()
					);
				}
				if($arPayment["PROPERTIES"]["STATUS_SERVICE"]["VALUE_ENUM"] == "Оплачено") {
					$ostatok = $ostatok - (double)$arPayment["PROPERTIES"]["SUM"]["VALUE"];
				}
			}
			?>
			<tr id="<?=$this->GetEditAreaId($arItem['ID']);?>">
				<td><a href="contract/<?= $arItem['ID'] ?>"><?= $arItem["PROPERTIES"]["NUMBER"]["VALUE"] ?></a></td>
				<td><?= GetMessage("LINKED_CONTRACTS_TABLE_ITEM_DATE") ?><?= $arItem["ACTIVE_FROM"] ?></td>
				<td><?= $arItem["PROPERTIES"]["LAWYER"]["VALUE"] ?></td>
				<td><?= $completionPercentage . GetMessage("LINKED_CONTRACTS_TABLE_ITEM_PERCENT") ?></td>
				<td><?= $arItem["PROPERTIES"]["CONTRACT_AMOUNT"]["VALUE"] ?></td>
				<td><?= $ostatok ?></td>
				<td><?= $arItem["PROPERTIES"]["STATUS"]["VALUE"] ?></td>
			</tr>
		<?endforeach;?>
	</table>
</div>

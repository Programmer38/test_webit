<?
//Добавляется в init.php событие на изменение/удаление элемента инфоблока
//ID инфоблока каталога = 3
AddEventHandler("iblock", "OnBeforeIBlockElementUpdate", array("UpdateItems", "NotUpdateItems"));
AddEventHandler("iblock", "OnBeforeIBlockElementDelete", array("UpdateItems", "NotDeleteItems"));
class UpdateItems
{
	//Задание 1
	function NotUpdateItems(&$arFields){
		$now = new DateTime();
		if($arFields['IBLOCK_ID'] == 3 && $arFields['DATE_CREATE'] > $now->modify('-7 day')->format('d.m.Y H:i:s'))){
			global $APPLICATION;
			$APPLICATION->throwException("Товар ".$arFields['DATE_CREATE']." был создан менее одной недели назад и не может быть изменен.");
			return false;
		}
	}
	//задание 2
	//Для отправки на почту менеджеру нужно еще создать почтовое событие и почтовый шаблон
	function NotDeleteItems($ID){
		$res = CIBlockElement::GetByID($ID);
		$item_element = $res->GetNext();
		if($item_element['SHOW_COUNTER']>10000 && $item_element['IBLOCK_ID'] == 3){
			$SITE_ID = 's1';
			$user_id = $USER->GetID();
			$user_name = $USER->GetLogin();
			$EVEN_TYPE = 'NotElementDelete'; // Тип почтового события
			$formFields = array(
				"USER_ID" => $user_id,
				"USER_NAME" => $user_name,
				"ITEM_NAME" => $item_element['NAME'],
				"ITEM_COUNTER" => $item_element['SHOW_COUNTER']
			);
			CEvent::Send($EVEN_TYPE, $SITE_ID, $formFields);

			global $APPLICATION;
			$APPLICATION->throwException("Нельзя удалить данный товар, так как он очень популярный на сайте");
			return false;
		}
	}
}

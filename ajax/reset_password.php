<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Mail\Event;
use Bitrix\Main\Web\Json;


function sanitizeInput($input)
{
	return htmlspecialcharsbx($input);
}

function generateRandomPassword($length = 10)
{
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$password = '';

	$password .= $characters[random_int(0, 9)];

	for ($i = 1; $i < $length; $i++) {
		$password .= $characters[random_int(0, strlen($characters) - 1)];
	}

	$password = str_shuffle($password);

	return $password;
}

// Проверка наличия и валидация email
if (isset($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
	$email = sanitizeInput($_POST['email']);

	// Поиск пользователя по email
	$user = CUser::GetByLogin($email)->Fetch();

	if ($user) {
		// Генерация нового пароля
		$newPassword = generateRandomPassword();

		// Обновление пароля пользователя
		$userUpdateFields = array(
			"PASSWORD" => $newPassword,
			"CONFIRM_PASSWORD" => $newPassword,
		);
		$userUpdate = new CUser;
		$userUpdate->Update($user["ID"], $userUpdateFields);

		// Отправка нового пароля на email
		$eventName = "RESET_PASSWORD_EVENT";
		$eventParams = array(
			"EMAIL" => $email,
			"NEW_PASSWORD" => $newPassword,
			"NAME" => $user["NAME"] . (!empty($user["SECOND_NAME"]) ? " " . $user["SECOND_NAME"] : "")
		);
		Event::send(array(
			"EVENT_NAME" => $eventName,
			"LID" => SITE_ID,
			"C_FIELDS" => $eventParams,
		));

		// Возвращаем успешный ответ
		$response = array(
			"success" => true,
			"message" => "Новый пароль успешно сгенерирован и отправлен на указанный email.",
		);
	} else {
		$response = array(
			"success" => false,
			"message" => "Пользователь с указанным email не найден.",
		);
	}
} else {
	$response = array(
		"success" => false,
		"message" => "Некорректный email.",
	);
}

echo Json::encode($response, JSON_UNESCAPED_UNICODE);

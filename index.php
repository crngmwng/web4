<?php
/**
 * Реализовать проверку заполнения обязательных полей формы в предыдущей
 * с использованием Cookies, а также заполнение формы по умолчанию ранее
 * введенными значениями.
 */

// Отправляем браузеру правильную кодировку,
// файл index.php должен быть в кодировке UTF-8 без BOM.
header('Content-Type: text/html; charset=UTF-8');

// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  // Массив для временного хранения сообщений пользователю.
  $messages = array();

  // В суперглобальном массиве $_COOKIE PHP хранит все имена и значения куки текущего запроса.
  // Выдаем сообщение об успешном сохранении.
  if (!empty($_COOKIE['save'])) {
    // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('save', '', 100000);
    // Если есть параметр save, то выводим сообщение пользователю.
    $messages[] = 'Спасибо, результаты сохранены.';
  }

  // Складываем признак ошибок в массив.
  $errors = array();
  $errors['name_empty'] = !empty($_COOKIE['name_error']);
	$errors['name_wrong'] = !empty($_COOKIE['name_err']);
	$errors['email'] = !empty($_COOKIE['email_error']);
	$errors['email_empty'] = !empty($_COOKIE['email_empty']);
	$errors['bio_empty'] = !empty($_COOKIE['bio_empty']);
  // TODO: аналогично все поля.

  // Выдаем сообщения об ошибках.
  if ($errors['name_empty']) {
    // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('name_error', '', 100000);
    // Выводим сообщение.
    $messages[] = '<div class="error">Заполните имя.</div>';
  }
	 if ($errors['name_wrong']) {
    setcookie('name_err', '', 100000);
    $messages[] = '<div class="error">Заполните имя правильно.</div>';
  }
	
  if ($errors['email']) {
    setcookie('email_error', '', 100000);
    $messages[] = '<div class="error">Введите корректный email.</div>';
  }
	if ($errors['email_empty']) {
    setcookie('email_error', '', 100000);
    $messages[] = '<div class="error">Введите email.</div>';
  }
	if($errors['bio_empty']) {
	setcookie('bio_empty', '', 100000);
    $messages[] = '<div class="error">Введите биографию.</div>';
	}
  // TODO: тут выдать сообщения об ошибках в других полях.

  // Складываем предыдущие значения полей в массив, если есть.
  $values = array();
  $values['name'] = empty($_COOKIE['name_value']) ? '' : $_COOKIE['name_value'];
$values['email'] = empty($_COOKIE['email_value']) ? '' : $_COOKIE['email_value'];
	$values['bio'] = empty($_COOKIE['bio_value']) ? '' : $_COOKIE['bio_value'];
  // TODO: аналогично все поля.

  // Включаем содержимое файла form.php.
  // В нем будут доступны переменные $messages, $errors и $values для вывода 
  // сообщений, полей с ранее заполненными данными и признаками ошибок.
  include('form.php');
}
// Иначе, если запрос был методом POST, т.е. нужно проверить данные и сохранить их в XML-файл.
else {
  // Проверяем ошибки.
  $errors = FALSE;
  if (empty($_POST['name'])) {
    // Выдаем куку на день с флажком об ошибке в поле fio.
    setcookie('name_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    // Сохраняем ранее введенное в форму значение на месяц.
    setcookie('name_value', $_POST['name'], time() + 30 * 24 * 60 * 60);
  }
if( !preg_match("/^[a-zа-яё]+$/i", $_POST['name'])) {
	setcookie('name_err', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
} 
	 else {
    setcookie('name_value', $_POST['name'], time() + 30 * 24 * 60 * 60);
  }


if (empty($_POST['email'])) {
 setcookie('email_empty', '1', time()+24*60*60);
  $errors = TRUE;
}
	else {
    setcookie('email_value', $_POST['email'], time() + 30 * 24 * 60 * 60);
  }

if (!preg_match("/^([a-z0-9_-]+\.)*[a-z0-9_-]+@[a-z0-9_-]+(\.[a-z0-9_-]+)*\.[a-z]{2,6}$/", $_POST['email'])){
  setcookie('email_error', '1', time()+24*60*60);
  $errors = TRUE;
}
	else {
    setcookie('email_value', $_POST['email'], time() + 30 * 24 * 60 * 60);
  }



	if (empty($_POST['bio'])) {
 setcookie('bio_empty', '1', time()+24*60*60);
  $errors = TRUE;
}
	else {
    setcookie('bio_value', $_POST['bio'], time() + 30 * 24 * 60 * 60);
  }


// *************
// TODO: тут необходимо проверить правильность заполнения всех остальных полей.
// Сохранить в Cookie признаки ошибок и значения полей.
// *************

  if ($errors) {
    // При наличии ошибок перезагружаем страницу и завершаем работу скрипта.
    header('Location: index.php');
    exit();
  }
  else {
    // Удаляем Cookies с признаками ошибок.
    setcookie('name_error', '', 100000);
	setcookie('email_error', '', 100000);
	setcookie('name_err', '', 100000);
	  setcookie('email_empty', '', 100000);
	  setcookie('bio_empty', '', 100000);
    // TODO: тут необходимо удалить остальные Cookies.
  }

  // Сохранение в БД.
  // ...
  $user = 'u47590';
$pass = '3205407';
$db = new PDO('mysql:host=localhost;dbname=u47590', $user, $pass, array(PDO::ATTR_PERSISTENT => true));

try {
  $stmt = $db->prepare("INSERT INTO application (name, email, year, sex, limbs, ability_immortality, ability_pass_thr_walls, ability_levitation, bio, checkbox ) 
  VALUES (:name, :email, :year, :sex, :limbs, :imm, :walls, :lev, :bio, :checkbox)");
  $stmt -> bindParam(':name', $name);
  $stmt -> bindParam(':email', $email);
  $stmt -> bindParam(':year', $year);
  $stmt -> bindParam(':sex', $sex);
  $stmt -> bindParam(':limbs', $limbs);
  $stmt -> bindParam(':imm', $imm);
  $stmt -> bindParam(':walls', $walls);
  $stmt -> bindParam(':lev', $lev);
  $stmt -> bindParam(':bio', $bio);
  $stmt -> bindParam(':checkbox', $checkbox);

  $name = $_POST['name'];
  $email = $_POST['email'];
  $year = $_POST['year'];
  $sex = $_POST['radio-group-1'];
  $limbs = $_POST['radio-group-2'];
	
   $imm = $_POST['power'];
   $walls = $_POST['power'];
   $lev = $_POST['power'];
	
  $bio = $_POST['bio'];

  if (empty($_POST['check-1']))
    $checkbox = "No";
  else
    $checkbox = $_POST['check-1'];

  
  $stmt -> execute();
}
catch(PDOException $e){
  print('Error : ' . $e->getMessage());
  exit();
}

  // Сохраняем куку с признаком успешного сохранения.
  setcookie('save', '1');

  // Делаем перенаправление.
  header('Location: index.php');
}

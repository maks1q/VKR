<?php
use Doctrine\DBAL\Connection;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\ParameterBag;

require_once __DIR__ . '/vendor/autoload.php';

Request::enableHttpMethodParameterOverride();

define("NOT_RECORDED", 1);
define("NOT_RECORDED_STRING", 'Не записывался');
define("IN_QUEUE", 2);
define("IN_QUEUE_STRING", 'В очереди на запись');
define("RECORDED", 3);
define("RECORDED_STRING", 'Записывается');
define("WAS_RECORDED", 4);
define("WAS_RECORDED_STRING", 'Записан');

$sapp = (new Application(['debug' => true]))
    ->register(new TwigServiceProvider(),
        ['twig.path' => __DIR__ . '/views'])
    ->register(new DoctrineServiceProvider(),
        ['db.options' => ['driver' => 'pdo_mysql', 'dbname' => 'diskrecord', 'charset' => 'utf8']]);	

//получить главную страницу
$sapp->get('/', function (Application $app) {
    $conn = $app['db'];
	$user = getUser($conn);
	if($user == null) 
	{	
		$conn->insert('user', ['login_user' => $login, 'name_user' => $login]);
		$user = $conn->fetchAssoc('select * from user where login_user = ?', [$login]);
		mkdir("./files/".$user["pk_user"], 0700);
	}
	$disks = $conn->fetchAll('select * from disk where fk_user = ?', [$user["pk_user"]]);
    return $app['twig']->render('main.html',['user' => $user, 'disks' => $disks]);
});

//получить страницу "Создание нового диска"
$sapp->get('/newdisk', function (Application $app) {
	$conn = $app['db'];
	$user = getUser($conn);	
    return $app['twig']->render('newdisk.html', ['user' => $user]);
});

//создать диск
$sapp->post('/newdisk', function (Application $app, Request $req) {
    $conn = $app['db'];
    $name = $req->get('disk-name');
	$desc = $req->get('disk-description');
	$type = 'CD';
	$user = getUser($conn);	
    $conn->insert('disk', ['name_disk' => $name, 'description_disk' => $desc, 'fk_user' => $user["pk_user"],
	'type_disk' => $type, 'status_disk' => NOT_RECORDED, 'status_string_disk' => NOT_RECORDED_STRING]);
	$id = $conn->lastInsertId();
	mkdir("./files/".$user["pk_user"]."/".$id, 0700);
	$uploaddir = './files/'.$user["pk_user"].'/'.$id.'/';
	foreach ($_FILES["files"]["error"] as $key => $error) {
		if ($error == UPLOAD_ERR_OK) {
			$uploadfile = $uploaddir.basename($_FILES["files"]["name"][$key]);
			$tmp_name = $_FILES["files"]["tmp_name"][$key];
			$type = $_FILES["files"]["type"][$key];
			$size = $_FILES["files"]["size"][$key];
			copy($tmp_name, $uploadfile);
			$conn->insert('file', ['name_file' => basename($_FILES["files"]["name"][$key]), 'path_file' => $_FILES["files"]["name"][$key], 'size_file' => $size, 'type_file' => $type, 'fk_disk' => $id]);
		}
	}	
    return $app->redirect('/');
});

//получить страницу "Файлы"
/*
$sapp->get('/loadfiles/{id}', function (Application $app, $id) {
	$conn = $app['db'];
	$login = $_SERVER['REMOTE_USER'];
	$encod = mb_detect_encoding($login, "windows-1251");
	if($encod == "Windows-1251") $login = iconv('Windows-1251','utf-8', $login);
	$user = $conn->fetchAssoc('select * from user where login_user = ?', [$login]);
	$disk = $conn->fetchAssoc('select * from disk where pk_disk = ?', [$id]);
    return $app['twig']->render('loadfiles.html', ['id' => $id]);
});

//загрузить файлы на сервер
$sapp->post('/loadfiles/{id}', function (Application $app, Request $req, $id) {
	$conn = $app['db'];
	$login = $_SERVER['REMOTE_USER'];
	$encod = mb_detect_encoding($login, "windows-1251");
	if($encod == "Windows-1251") $login = iconv('Windows-1251','utf-8', $login);
	$user = $conn->fetchAssoc('select * from user where login_user = ?', [$login]);	
	$uploaddir = './files/'.$user["pk_user"].'/'.$id.'/';
	foreach ($_FILES["uploads"]["error"] as $key => $error) {
		if ($error == UPLOAD_ERR_OK) {
			$uploadfile = $uploaddir.basename($_FILES["uploads"]["name"][$key]);
			$tmp_name = $_FILES["uploads"]["tmp_name"][$key];
			copy($tmp_name, $uploadfile);
			$conn->insert('file', ['path_file' => basename($_FILES["uploads"]["name"][$key]), 'fk_disk' => $id]);
		}
	}	
    return $app->redirect('/');
});*/
	
//получить страницу "Настройки"
$sapp->get('/settings', function (Application $app) {
	$conn = $app['db'];
	$user = getUser($conn);
    return $app['twig']->render('settings.html', ['user' => $user]);
});

//сохранение настроек
$sapp->post('/settings/{id}', function (Application $app, Request $req, $id) {
    $conn = $app['db'];
    $name = $req->get('user-name');
	$conn->update('user', ['name_user' => $name], ['pk_user' => $id]);
	return $app->redirect('/');
});

//получить страницу диска
$sapp->get('/disk/{id}', function (Application $app, $id) {
    $conn = $app['db'];
    $disk = $conn->fetchAssoc('select * from disk where pk_disk = ?', [$id]);
    if (!$disk) {
        throw new NotFoundHttpException("Такой диск отсутствует - $id");
    }
	$user = getUser($conn);
    $files = $conn->fetchAll('select * from file where fk_disk = ?', [$id]);
    return $app['twig']->render('disk.html', ['disk' => $disk , 'user' => $user, 'files' => $files]);
});

//получить страницу редактирования диска
$sapp->get('/editdisk/{id}', function (Application $app, $id) {
    $conn = $app['db'];
    $disk = $conn->fetchAssoc('select * from disk where pk_disk = ?', [$id]);
	//$prod = $conn->fetchAll('select * from producer');
	$user = getUser($conn);
    return $app['twig']->render('editdisk.html', ['disk' => $disk, 'user' => $user]);
});

//сохранить редактирование диска
$sapp->post('/editdisk/{id}', function (Application $app, Request $req, $id) {
    $conn = $app['db'];
	$name = $req->get('disk-name');
	$desc = $req->get('disk-description');
	$type = 'CD';
	$user = getUser($conn);	
	$conn->update('disk', ['name_disk' => $name, 'description_disk' => $desc], ['pk_disk' => $id]);
	return $app->redirect('/');
});

/*
$sapp->delete('/disk/{id}', function (Application $app, $id) {
    $conn = $app['db'];
	$conn->delete('file', ['fk_disk' => $id]);
    $conn->delete('disk', ['pk_disk' => $id]);
    return $app->redirect('/');
});
*/

//AJAX удаление диска 
$sapp->delete('/', function (Request $request) use ($sapp) {
    $conn = $sapp['db'];
	$id = $request->request->get('id');
	$conn->delete('file', ['fk_disk' => $id]);
    $conn->delete('disk', ['pk_disk' => $id]);
	return $sapp->json("Удаление прошло успешно!", 200);
});

//AJAX отправка диска на запись
$sapp->post('/', function (Request $request) use ($sapp) {
	$conn = $sapp['db'];
	$id = $request->request->get('id');
	date_default_timezone_set('Asia/Krasnoyarsk');
	$date = date("Y-m-d H:i:s");
	$conn->update('disk', ['status_disk' => IN_QUEUE, 'status_string_disk' => IN_QUEUE_STRING], ['pk_disk' => $id]);
	$conn->insert('record', ['date_record' => $date, 'status_record' => IN_QUEUE, 'status_string_record' => IN_QUEUE_STRING,'success_flag' => false,
	'error_flag' => false, 'success_print_flag' => false, 'error_print_flag' => false , 'fk_disk' => $id]);
	return $sapp->json("Диск отправлен на запись!", 200);
});

//AJAX копирование диска
$sapp->put('/', function (Request $request) use ($sapp) {
	$conn = $sapp['db'];
	$user = getUser($conn);	
	$id = $request->request->get('id');
	$disk = $conn->fetchAssoc('select * from disk where pk_disk = ?', [$id]);
	$conn->insert('disk', ['name_disk' => $disk.name_disk, 'description_disk' => $disk.description_disk, 'fk_user' => $disk.fk_user,
	'type_disk' => $disk.type_disk, 'status_disk' => NOT_RECORDED, 'status_string_disk' => NOT_RECORDED_STRING]);
	$files = $conn->fetchAll('select * from file where fk_disk = ?', [$id]);
	$id1 = $conn->lastInsertId();
	mkdir("./files/".$user["pk_user"]."/".$id1, 0700);
	$uploaddir = './files/'.$user["pk_user"].'/'.$id1.'/';
	foreach ($files as $f) {
		copy($f.name_file, $uploadfile);
		$conn->insert('file', ['name_file' => $f.name_file, 'path_file' => $f.path_file, 'size_file' => $f.size_file, 'type_file' => $f.type_file, 'fk_disk' => $id1]);
	}
	return $sapp->json("Диск скопирован!", 200);
});

//функция получения пользователя
function getUser($c) {
    $login = $_SERVER['REMOTE_USER'];
	$encod = mb_detect_encoding($login, "windows-1251");
	if($encod == "Windows-1251") $login = iconv('Windows-1251','utf-8', $login);
	$user = $c->fetchAssoc('select * from user where login_user = ?', [$login]);
    return $user;
}

$sapp->run();	
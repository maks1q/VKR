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

$sapp = (new Application(['debug' => true]))
    ->register(new TwigServiceProvider(),
        ['twig.path' => __DIR__ . '/views'])
    ->register(new DoctrineServiceProvider(),
        ['db.options' => ['driver' => 'pdo_mysql', 'dbname' => 'diskrecord', 'charset' => 'utf8']]);	

//получить главную страницу
$sapp->get('/', function (Application $app) {
    $conn = $app['db'];
	$login = $_SERVER['REMOTE_USER'];
	$encod = mb_detect_encoding($login, "windows-1251");
	if($encod == "Windows-1251") $login = iconv('Windows-1251','utf-8', $login);
	$user = $conn->fetchAssoc('select * from user where login_user = ?', [$login]);
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
	$login = $_SERVER['REMOTE_USER'];
	$encod = mb_detect_encoding($login, "windows-1251");
	if($encod == "Windows-1251") $login = iconv('Windows-1251','utf-8', $login);
	$user = $conn->fetchAssoc('select * from user where login_user = ?', [$login]);	
    return $app['twig']->render('newdisk.html', ['user' => $user]);
});

//создать диск
$sapp->post('/newdisk', function (Application $app, Request $req) {
    $conn = $app['db'];
    $name = $req->get('disk-name');
	$desc = $req->get('disk-description');
	$type = 'CD';
	$login = $_SERVER['REMOTE_USER'];
	$encod = mb_detect_encoding($login, "windows-1251");
	if($encod == "Windows-1251") $login = iconv('Windows-1251','utf-8', $login);
	$user = $conn->fetchAssoc('select * from user where login_user = ?', [$login]);	
    $conn->insert('disk', ['name_disk' => $name, 'description_disk' => $desc, 'fk_user' => $user["pk_user"], 'type_disk' => $type]);
	$id = $conn->lastInsertId();
	mkdir("./files/".$user["pk_user"]."/".$id, 0700);
	$uploaddir = './files/'.$user["pk_user"].'/'.$id.'/';
	foreach ($_FILES["files"]["error"] as $key => $error) {
		if ($error == UPLOAD_ERR_OK) {
			$uploadfile = $uploaddir.basename($_FILES["files"]["name"][$key]);
			$tmp_name = $_FILES["files"]["tmp_name"][$key];
			copy($tmp_name, $uploadfile);
			$conn->insert('file', ['path_file' => basename($_FILES["files"]["name"][$key]), 'fk_disk' => $id]);
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
	$login = $_SERVER['REMOTE_USER'];
	$encod = mb_detect_encoding($login, "windows-1251");
	if($encod == "Windows-1251") $login = iconv('Windows-1251','utf-8', $login);
	$user = $conn->fetchAssoc('select * from user where login_user = ?', [$login]);
    return $app['twig']->render('settings.html', ['user' => $user]);
});

//сохранение настроек
$sapp->post('/settings/{id}', function (Application $app, Request $req, $id) {
    $conn = $app['db'];
    $name = $req->get('user-name');
	$conn->update('user', ['name_user' => $name], ['pk_user' => $id]);
	return $app->redirect('/');
});

$sapp->delete('/disk/{id}', function (Application $app, $id) {
    $conn = $app['db'];
	$conn->delete('file', ['fk_disk' => $id]);
    $conn->delete('disk', ['pk_disk' => $id]);
    return $app->redirect('/');
});

$sapp->delete('/', function (Request $request) use ($sapp) {
    $conn = $sapp['db'];
	$id = $request->request->get('id');
	$conn->delete('file', ['fk_disk' => $id]);
    $conn->delete('disk', ['pk_disk' => $id]);
	return $sapp->json("Удаление прошло успешно!", 200);
});

$sapp->run();	
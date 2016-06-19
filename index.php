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
	if($name == null) $name = 'Диск';
	$desc = $req->get('disk-description');
	$type = '...';
	$user = getUser($conn);	
	$size_disk = 0;
    $conn->insert('disk', ['name_disk' => $name, 'description_disk' => $desc, 'fk_user' => $user["pk_user"],
	'type_disk' => $type, 'size_disk' => $size_disk, 'status_disk' => NOT_RECORDED, 'status_string_disk' => NOT_RECORDED_STRING]);
	$id = $conn->lastInsertId();
	mkdir("./files/".$user["pk_user"]."/".$id, 0700);
	$uploaddir = './files/'.$user["pk_user"].'/'.$id.'/';
	foreach ($_FILES["files"]["error"] as $key => $error) {
		if ($error == UPLOAD_ERR_OK) {
			$uploadfile = $uploaddir.basename($_FILES["files"]["name"][$key]);
			$tmp_name = $_FILES["files"]["tmp_name"][$key];
			$type = $_FILES["files"]["type"][$key];
			$size = $_FILES["files"]["size"][$key];
			$size_disk = $size_disk + $size;
			copy($tmp_name, $uploadfile);
			$conn->insert('file', ['name_file' => basename($_FILES["files"]["name"][$key]), 'size_file' => $size, 'type_file' => $type, 'fk_disk' => $id]);
		}
	}
	if($size_disk < 700000000) {
		$type = 'CD';
	}
	else {
		$type = 'DVD';
	}
	$conn->update('disk', ['type_disk' => $type, 'size_disk' => $size_disk], ['pk_disk' => $id]);
    return $app->redirect('/');
});

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
	$user = getUser($conn);
	$files = $conn->fetchAll('select * from file where fk_disk = ?', [$id]);
    return $app['twig']->render('editdisk.html', ['disk' => $disk, 'user' => $user, 'files' => $files]);
});

//сохранить редактирование диска
$sapp->post('/editdisk/{id}', function (Application $app, Request $req, $id) {
    $conn = $app['db'];
	$name = $req->get('disk-name');
	if($name == null) $name = 'Диск';
	$desc = $req->get('disk-description');
	$user = getUser($conn);	
	$disk = $conn->fetchAssoc('select * from disk where pk_disk = ?', [$id]);
	$size_disk = $disk["size_disk"];
	$conn->update('disk', ['name_disk' => $name, 'description_disk' => $desc], ['pk_disk' => $id]);
	$uploaddir = './files/'.$user["pk_user"].'/'.$id.'/';
	foreach ($_FILES["files"]["error"] as $key => $error) {
		if ($error == UPLOAD_ERR_OK) {
			$uploadfile = $uploaddir.basename($_FILES["files"]["name"][$key]);
			$tmp_name = $_FILES["files"]["tmp_name"][$key];
			$type = $_FILES["files"]["type"][$key];
			$size = $_FILES["files"]["size"][$key];
			$size_disk = $size_disk + $size;
			copy($tmp_name, $uploadfile);
			$conn->insert('file', ['name_file' => basename($_FILES["files"]["name"][$key]), 'size_file' => $size, 'type_file' => $type, 'fk_disk' => $id]);
		}
	}
	if($size_disk < 700000000) {
		$type = 'CD';
	}
	else {
		$type = 'DVD';
	}
	$conn->update('disk', ['type_disk' => $type, 'size_disk' => $size_disk], ['pk_disk' => $id]);	
	return $app->redirect('/');
});

//получить страницу очереди
$sapp->get('/queue', function (Application $app) {
    $conn = $app['db'];
	$status = 0;
	$str = 0;
	$command = '';
	$record_queue = $conn->fetchAssoc('select * from queue where pk_queue = ?', [1]);
	$records = $conn->fetchAll('select d.name_disk, d.type_disk, d.status_string_disk, r.status_record, r.status_string_record,
	r.date_record, u.pk_user, u.login_user from record r, disk d, user u where r.status_record != ? and r.fk_disk = d.pk_disk and d.fk_user = u.pk_user', [4]);
	$rec = $conn->fetchAssoc('select u.pk_user, d.pk_disk, d.name_disk, r.pk_record from record r, disk d, user u where r.pk_record = ? and r.fk_disk = d.pk_disk and d.fk_user = u.pk_user', [$record_queue["id_record"]]);
	if($rec != null)
		$command = '"C:/Program Files (x86)/Nero/Nero 7/Core/NeroCmd.exe" --write  --drivename d --real --iso '.$rec["name_disk"].' --speedtest --close_session --underrun_prot --create_udf_fs --dvd_high_compatibility ./files/'.$rec["pk_user"].'/'.$rec["pk_disk"].'/*.*';
	if($record_queue["status_queue"] == 0){
		$status = 0;
	}
	else if($record_queue["status_queue"] == 1){
		$status = 1;		
	} 
	else if($record_queue["status_queue"] == 2){
		$status = 2;
		if($record_queue["progon"] == 1){
			$conn->update('queue', ['progon' => 0], ['pk_queue' => 1]);
			$conn->update('record', ['status_record' => RECORDED, 'status_string_record' => RECORDED_STRING], ['pk_record' => $rec["pk_record"]]);
			$conn->update('disk', ['status_disk' => RECORDED, 'status_string_disk' => RECORDED_STRING], ['pk_disk' => $rec["pk_disk"]]);
		}
		else{
			exec($command);
			$conn->update('record', ['status_record' => WAS_RECORDED, 'status_string_record' => WAS_RECORDED_STRING], ['pk_record' => $rec["pk_record"]]);
			$conn->update('disk', ['status_disk' => WAS_RECORDED, 'status_string_disk' => WAS_RECORDED_STRING], ['pk_disk' => $rec["pk_disk"]]);
			$records = $conn->fetchAll('select d.name_disk, d.type_disk, d.pk_disk, d.status_string_disk, r.status_record, r.status_string_record, r.date_record, r.pk_record,
			u.pk_user, u.login_user from record r, disk d, user u where r.status_record != ? and r.fk_disk = d.pk_disk and d.fk_user = u.pk_user', [4]);
			if($records != null) {
				$pk_disk = 0;
				$pk_record = 0;
				$date = 0;
				foreach($records as $r) {
					if($pk_record == 0 && $date == 0) {
						$pk_record = $r["pk_record"];
						$date = $r["date_record"];
						$pk_disk = $r["pk_disk"];
					}
					else {
						if($date > $r["date_record"]) {
							$pk_record = $r["pk_record"];
							$date = $r["date_record"];
							$pk_disk = $r["pk_disk"];
						}
					}
				}
				$conn->update('queue', ['status_queue' => 2, 'id_record' => $pk_record, 'progon' => 1], ['pk_queue' => 1]);
				$conn->update('record', ['status_record' => RECORDED, 'status_string_record' => RECORDED_STRING], ['pk_record' => $pk_record]);
				$conn->update('disk', ['status_disk' => RECORDED, 'status_string_disk' => RECORDED_STRING], ['pk_disk' => $pk_disk]);
			}
			else {		
				$conn->update('queue', ['status_queue' => 0, 'id_record' => 0, 'progon' => 0], ['pk_queue' => 1]);
			}
		}
	}
	//$user = getUser($conn);
	
	$data = date("Y-m-d H:i:s");
    return $app['twig']->render('queue.html',['data' => $data, 'records' => $records, 'status' => $status, 'command' => $command]);
});

//AJAX удаление диска 
$sapp->delete('/', function (Request $request) use ($sapp) {
    $conn = $sapp['db'];
	$id = $request->request->get('id');
	$user = getUser($conn);
	$files = $conn->fetchAll('select * from file where fk_disk = ?', [$id]);
	$dir = './files/'.$user["pk_user"].'/'.$id.'/';
	foreach($files as $f) {
		$filename = $dir.$f["name_file"];
		unlink($filename);		
	}
	rmdir($dir);
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
	$record_id = $conn->lastInsertId();
	$record_queue = $conn->fetchAssoc('select * from queue where pk_queue = ?', [1]);
	if($record_queue["status_queue"] == 0){
		$conn->update('disk', ['status_disk' => RECORDED, 'status_string_disk' => RECORDED_STRING], ['pk_disk' => $id]);
		$conn->update('queue', ['status_queue' => 2, 'id_record' => $record_id, 'progon' => 1], ['pk_queue' => 1]);
		$conn->update('record', ['status_record' => RECORDED, 'status_string_disk' => RECORDED_STRING], ['pk_record' => $record_id]);
	}
	return $sapp->json("Диск отправлен на запись!", 200);
});

//AJAX копирование диска
$sapp->post('/copy', function (Request $request) use ($sapp) {
	$conn = $sapp['db'];
	$user = getUser($conn);	
	$id = $request->request->get('id');
	$disk = $conn->fetchAssoc('select * from disk where pk_disk = ?', [$id]);
	$conn->insert('disk', ['name_disk' => $disk["name_disk"], 'description_disk' => $disk["description_disk"], 'fk_user' => $disk["fk_user"],
	'type_disk' => $disk["type_disk"], 'status_disk' => NOT_RECORDED, 'status_string_disk' => NOT_RECORDED_STRING]);
	$id1 = $conn->lastInsertId();
	$files = $conn->fetchAll('select * from file where fk_disk = ?', [$id]);
	mkdir("./files/".$user["pk_user"]."/".$id1, 0700);
	$olddir = './files/'.$user["pk_user"].'/'.$id.'/';
	$uploaddir = './files/'.$user["pk_user"].'/'.$id1.'/';
	foreach ($files as $f) {
		$source = $olddir.$f["name_file"];
		$dest = $uploaddir.$f["name_file"];
		copy($source, $dest);
		$conn->insert('file', ['name_file' => $f["name_file"], 'path_file' => $f["path_file"], 'size_file' => $f["size_file"], 'type_file' => $f["type_file"], 'fk_disk' => $id1]);
	}	
	$disk1 = $conn->fetchAssoc('select * from disk where pk_disk = ?', [$id1]);
    return $sapp->json(array('disk' => $disk1), 200);
	//return $sapp->json("Диск скопирован!", 200);
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
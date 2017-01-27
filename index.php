<?php 
require 'vendor/autoload.php';
require 'libs/NotORM.php'; 
//membuat dan mengkonfigurasi slim app
$app = new \Slim\app;

// konfigurasi database
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = 'thekos';
$dbmethod = 'mysql:dbname=';

$dsn = $dbmethod.$dbname;
$pdo = new PDO($dsn, $dbuser, $dbpass);
$db  = new NotORM($pdo);

//mendefinisikan route app di home
$app-> get('/', function(){
    echo "error_bro";
});


//Json To maps
$app ->get('/home_maps', function()use($app,$db){
    foreach ($db->home_maps() as $data) {
        $home_maps[] = array(
            'id' => $data['id'],
            'nama' => $data['nama'],
            'alamat' => $data['alamat'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'harga' => $data['harga'],
            'icon' => $data['icon']
            );
    }
    echo json_encode($home_maps);
});

//Json List Kost
$app ->get('/list_area', function()use($app,$db){
    foreach ($db ->list_area() as $data ) {
        $list_area[] = array(
            'id' => $data['id'],
            'lokasi' => $data['lokasi'],
            'jumlah' => $data['jumlah'],
            'icon' => $data['icon'] 
            );
    }
    echo json_encode($list_area);
});


//Json List Kost
$app ->get('/data_adlp', function()use($app,$db){
    foreach ($db ->data_adlp() as $data ) {
        $data_adlp[] = array(
            'id' => $data['id'],
            'nama' => $data['nama'],
            'no_hp' =>$data['no_hp']
            );
    }
    echo json_encode($data_adlp);
});


//lokasi
$app ->get('/lokasi/{offset}', function($requset, $response, $args)use($app,$db){
    $offset = $args['offset'];
    $query = $db->lokasi()->order("id DESC")->limit(10,$offset);
    $count = $query->count();
    $json_kosong = 0;
    if ($count<10) {
        if ($count==0) {
            $json_kosong = 1;
        } else {
            $query = $db->lokasi()->order("id DESC")->limit($count,$offset); 
            $count = $query->count();
            if (empty($count)) {
                $query = $db->lokasi()->order("id DESC")->limit(0,$offset); 
                $num = 0;
            } else {
                $num = $offset;
            }
        }
    }else{
        $num = $offset;
    }

    foreach($query as $data){
        $num++;
        $lokasi[] = array(
            'no'=> $num,
            'id' => $data ['id'],
            'title' => $data['nama'],
            'imgurl' => $data['foto']
            );
    }

    if($json_kosong==1){
        $lokasi[] = array(
            'no'=> " ",
            'id' => " ",
            'title' => " ",
            'imgurl' => " "
            );
    }
    echo json_encode($lokasi);

});

$c = $app->getContainer();
$c['errorHandler'] = function ($c) {
    return function ($request, $response, $exception) use ($c) {
        return $c['response']
            ->withStatus(500)
            ->withHeader('Content-Type', 'text/html')
            ->write('error_bro');
    };
};


//run App
$app->run();
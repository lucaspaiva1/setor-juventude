<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

// veja: https://developers.facebook.com/docs/php/gettingstarted/
require_once __DIR__ . '/sdk/src/Facebook/autoload.php';
require_once __DIR__ . '/sdk/src/Facebook/Facebook.php';
require_once __DIR__ . '/sdk/src/Facebook/FacebookRequest.php';
require_once __DIR__ . '/sdk/src/Facebook/FacebookApp.php';

use Facebook\FacebookRequest;

$the_request = &$_POST;

$postdata = file_get_contents("php://input");

if (isset($postdata)) {

    $request = json_decode($postdata);
    $user_token = $request->token;
    $user_id = $request->id;
    $publicacao = $request->publicacao;

    //fazendo post para pegar informacoes das paginas do usuario
    $resp = file_get_contents("https://graph.facebook.com/" . $user_id . "/accounts?access_token=" . $user_token);

    $pagesInfo = json_decode($resp); //reposta do post

    echo json_encode($pagesInfo);

    //percorrendo a lista de paginas ate encontrar a pagina correta
    foreach ($pagesInfo->data as $page) {
        if ($page->name == "Teeste") {
            $page_token = $page->access_token;
            //echo json_encode($page->access_token);
        }
    }

    //criando instancia
    $fb = new Facebook\Facebook([
        'app_id' => '255509854899402',
        'app_secret' => 'd390eaebdc7e8b2078714e4f0fd65616',
        'default_graph_version' => 'v2.8',
    ]);

    //dados do que vai ser postado

    $data = [
        'message' => $publicacao->Titulo."\n ".$publicacao->Texto
    ];

    //publicando dados como a pagina
    try {
        // Returns a `Facebook\FacebookResponse` object //page id: 1340714515951972
        $response = $fb->post('/400448263669806/feed', $data, $page_token);
    } catch (Facebook\Exceptions\FacebookResponseException $e) {
        echo json_encode('Graph returned an error: ' . $e->getMessage());
        exit;
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
        echo json_encode('$token Facebook SDK returned an error: ' . $e->getMessage());
        exit;
    }

    //reposta do facebook
    $graphNode = $response->getGraphNode();

    echo json_encode('Post ID: ' . $graphNode['id']);

}
?>
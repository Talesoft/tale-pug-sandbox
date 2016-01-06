<?php

use Tale\Jade\Renderer,
    Tale\Jade\Compiler;

include 'vendor/autoload.php';

define('VIEW_PATH', __DIR__.'/views');
define('EXAMPLE_PATH', __DIR__.'/examples');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $compilerOptions = [
        'pretty' => isset($_POST['pretty']) ? $_POST['pretty'] === 'true' : true,
        'standAlone' => isset($_POST['standAlone']) ? $_POST['standAlone'] === 'true' : false,
        'allowImports' => false
    ];

    $compiler = new Compiler($compilerOptions);
    $jade = isset($_POST['jade']) ? $_POST['jade'] : '';

    $result = null;
    try {

        $result = $compiler->compile($jade);
    } catch(\Exception $e) {

        $result = "\n".get_class($e)."\n\n".$e->getMessage();
    }

    header('Content-Type: application/json; encoding=utf-8');
    echo json_encode($result);
    exit;
}




$view = isset($_GET['view']) ? $_GET['view'] : 'index';
$example = isset($_GET['example']) ? $_GET['example'] : 'index';


if (!file_exists(VIEW_PATH."/$view.jade"))
    $view = 'index';

if (!file_exists(EXAMPLE_PATH."/$example.jade"))
    $example = 'index';

$renderer = new Renderer([
    'paths' => [__DIR__.'/views'],
    'pretty' => false,
    'adapterOptions' => [
        'lifeTime' => 3600 * 24
    ]
]);



echo $renderer->render('index', [
    'exampleCode' => json_encode(file_get_contents(EXAMPLE_PATH."/$example.jade")),
    'currentExample' => $example
]);
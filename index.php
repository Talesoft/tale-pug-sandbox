<?php

use Tale\Jade\Lexer;
use Tale\Jade\Parser;
use Tale\Jade\Renderer;
use Tale\Jade\Compiler;

include 'vendor/autoload.php';

define('VIEW_PATH', __DIR__.'/views');
define('EXAMPLE_PATH', __DIR__.'/examples');
define('SAVE_PATH', __DIR__.'/saves');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    header('Content-Type: application/json; encoding=utf-8');

    $jade = isset($_POST['jade']) ? $_POST['jade'] : '';
    $mode = isset($_POST['mode']) ? $_POST['mode'] : '';

    switch ($mode) {
        case 'save':

            $id = null;
            $path = null;
            do {

                $id = uniqid();
                $path = SAVE_PATH.'/'.implode('/', str_split($id)).'.jade';
            } while(file_exists($path));

            $dir = dirname($path);

            if (!is_dir($dir))
                mkdir($dir, 0755, true);

            file_put_contents($path, $jade);

            echo json_encode(['success' => true, 'id' => $id]);
            exit;
        case 'compile':

            $compilerOptions = [
                'pretty' => isset($_POST['pretty']) ? $_POST['pretty'] === 'true' : false,
                'standAlone' => isset($_POST['standAlone']) ? $_POST['standAlone'] === 'true' : false,
                'allowImports' => false
            ];

            $compiler = new Compiler($compilerOptions);
            $result = null;
            try {

                $result = $compiler->compile($jade);
            } catch(\Exception $e) {

                echo json_encode(['success' => false, 'message' => "\n".get_class($e)."\n\n".$e->getMessage()]);
                exit;
            }

            echo json_encode(['success' => true, 'output' => $result]);
            exit;
        case 'lex':

            $lexer = new Lexer();
            $result = null;
            try {

                ob_start();
                $lexer->dump($jade);
                $result = ob_get_clean();
            } catch(\Exception $e) {

                echo json_encode(['success' => false, 'message' => "\n".get_class($e)."\n\n".$e->getMessage()]);
                exit;
            }

            echo json_encode(['success' => true, 'output' => $result]);
            exit;
        case 'parse':

            $parser = new Parser();
            $result = null;
            try {

                $result = $parser->parse($jade);
            } catch(\Exception $e) {

                echo json_encode(['success' => false, 'message' => "\n".get_class($e)."\n\n".$e->getMessage()]);
                exit;
            }

            echo json_encode(['success' => true, 'output' => (string)$result]);
            exit;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid mode selected']);
    }
}




$view = isset($_GET['view']) ? $_GET['view'] : 'index';
$example = isset($_GET['example']) ? $_GET['example'] : 'index';
$id = isset($_GET['id']) ? $_GET['id'] : null;


if (!file_exists(VIEW_PATH."/$view.jade"))
    $view = 'index';

$jade = '';
if ($id && preg_match('/^[a-z0-9]+$/i', $id)) {

    $path = SAVE_PATH.'/'.implode('/', str_split($id)).'.jade';
    if (file_exists($path))
        $jade = file_get_contents($path);
    else
        $id = null;

} else if ($example && preg_match('/^[a-z0-9\-]+$/i', $example) && $example !== 'empty' && file_exists(EXAMPLE_PATH."/$example.jade")) {

    $jade = file_get_contents(EXAMPLE_PATH."/$example.jade");
    $id = null;
}



$renderer = new Renderer([
    'paths' => [__DIR__.'/views'],
    'pretty' => false,
    'adapterOptions' => [
        'lifeTime' => 3600 * 24
    ]
]);



echo $renderer->render('index', [
    'jade' => $jade,
    'example' => $example,
    'id' => $id
]);
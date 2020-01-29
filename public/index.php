<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use \Source\TemplateEngine as TE;
use \Source\UserService as US;
use \Source\BlogService as BS;

require __DIR__ . '/../vendor/autoload.php';

$checkLogin = function (Request $request, RequestHandlerInterface $handler){
    if(empty($_SESSION['isLogged']) || $_SESSION['isLogged'] !== true){
        $response = new \Slim\Psr7\Response();
        return $response->withHeader("Location", "login");
    }
    return $handler->handle($request);
};

$app = AppFactory::create();
session_start();

// To help the built-in PHP dev server, check if the request was actually for
// something which should probably be served as a static file
if (PHP_SAPI == 'cli-server') {
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) return false;
}

$app->get('/', function (Request $request, Response $response, array $args) {
    $postsList = "";
    $te = new TE("../templates/fullpage.template");
    $te->addVariable("title", "Home");
    $list = new TE("../templates/list.template");
    $list->addVariable("posts", $postsList);
    $te->addVariable("content", $list->render());
    $response->getBody()->write($te->render());
    return $response;
});
$app->get('/register', function (Request $request, Response $response, array $args) {
    $te = new TE("../templates/fullpage.template");
    $te->addVariable("title", "Register");
    $reg = new TE("../templates/user.template");
    $reg->addVariable("title", "Register");
    $reg->addVariable("url", "register");
    $te->addVariable("content", $reg->render());
    $response->getBody()->write($te->render());
    return $response;
});
$app->post('/register', function (Request $request, Response $response, array $args) {
    $us = new US();
    if($us->userExists($_POST['username'])){
        return $response->withHeader('Location', 'register');
    }else{
        //$_SESSION['usuarios'][$_POST['username']] = $_POST['password'];
        $_SESSION['username'] = $_POST['username'];
        $_SESSION['isLogged'] = true;
        $us->saveUser($_POST['username']);
    } 
    return $response->withHeader('Location', '/');
});
$app->get('/login', function (Request $request, Response $response, array $args) {
    $te = new TE("../templates/fullpage.template");
    $te->addVariable("title", "Login");
    $log = new TE("../templates/user.template");
    $log->addVariable("title", "login");
    $log->addVariable("url", "login");
    $te->addVariable("content", $log->render());
    $response->getBody()->write($te->render());
    return $response;
});
$app->post('/login', function (Request $request, Response $response, array $args) {
    $us = new US();
    if (!isset($_POST['logout'])) {
        if ($us->userExists($_POST['username'])) {
            $_SESSION['username'] = $_POST['username'];
            $_SESSION['isLogged'] = true;
            return $response->withHeader('Location', '/');
        } else {
            return $response->withHeader('Location', 'login');
        }
    } else {
        $_SESSION['isLogged'] = false;
        $_SESSION['username'] = null;
        return $response->withHeader('Location', 'login');
    }
    return $response->withHeader('Location', 'login');
});
$app->get('/user/{username}', function (Request $request, Response $response, array $args) {
    if ($args['username'] == $_SESSION['username']){
        return $response->withHeader("Location", "me");
    }
    $te = new TE("../templates/fullpage.template");
    $te->addVariable("title", $args['username']);
    $te->addVariable("content", $args['username']);
    $bs = new BS();
    $postsList = "";
    $allposts = $bs->getAllPosts($args['username']);
    foreach($allposts as $v){
        $newPost = new TE("../templates/post.template");
        $newPost->addVariable("content", $v);
        $postsList .= $newPost->render();
    }
    $list = new TE("../templates/list.template");
    $list->addVariable("posts", $postsList);
    $te->addVariable("content", $list->render());
    
    $response->getBody()->write($te->render());
    return $response;
});
$app->get('/me', function (Request $request, Response $response, array $args) {
    $te = new TE("../templates/fullpage.template");
    $te->addVariable("title", $_SESSION['username']);
    $bs = new BS();
    $allposts = $bs->getAllPosts($_SESSION['username']);
    $postsList = "";
    foreach($allposts as $v){
        $newPost = new TE("../templates/post.template");
        $newPost->addVariable("content", $v);
        $postsList .= $newPost->render();
    }
    $me = new TE("../templates/me.template");
    $me->addVariable("posts", $postsList);
    $me->addVariable("username", $_SESSION['username']);
    $te->addVariable("content", $me->render());
    
    $response->getBody()->write($te->render());
    return $response;
})->add($checkLogin);

$app->post('/newPost', function (Request $request, Response $response, array $args) {
    $bs = new BS();
    $bs->savePost($_POST['postmsg'], $_POST['username']);
    return $response->withHeader('Location', '/me');
})->add($checkLogin);



$app->run();
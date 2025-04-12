<?php
use React\Http\Message\Response;

// Función para manejar las rutas con el mismo patrón
function renderPage($app, $route, $filePath) {
    $app->get($route, function () use ($filePath) {
        ob_start();
        include $filePath;
        $content = ob_get_clean();
        return new Response(200, ['Content-Type' => 'text/html'], $content);
    });
}

// Rutas para index.php
$indexRoutes = ['/', '/home', 'Index', '/index.php', '/Index.php'];
foreach ($indexRoutes as $route) {
    renderPage($app, $route, __DIR__ . '/../../public/index.php');
}

// Rutas específicas
renderPage($app, '/test', __DIR__ . '/../../public/test.php');
renderPage($app, '/LogWeb', __DIR__ . '/../../public/views/logWebHook.php');
renderPage($app, '/Payment/New', __DIR__ . '/../../public/views/compra/nueva.php');
renderPage($app, '/Payment/List', __DIR__ . '/../../public/views/compra/consulta.php');
renderPage($app, '/Users', __DIR__ . '/../../public/views/usuario.php');
?>
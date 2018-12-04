<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\Routing\Route\DashedRoute;

/**
 * The default class to use for all routes
 *
 * The following route classes are supplied with CakePHP and are appropriate
 * to set as the default:
 *
 * - Route
 * - InflectedRoute
 * - DashedRoute
 *
 * If no call is made to `Router::defaultRouteClass()`, the class used is
 * `Route` (`Cake\Routing\Route\Route`)
 *
 * Note that `Route` does not do any inflections on URLs which will result in
 * inconsistently cased URLs when used with `:plugin`, `:controller` and
 * `:action` markers.
 *
 */
Router::defaultRouteClass(DashedRoute::class);

Router::scope('/', function (RouteBuilder $routes) {
    /** @var Router $routes */
    $routes->extensions(['json']);

    $routes->connect('/', ['controller' => 'Pages', 'action' => 'home']);
    $routes->connect('/about', ['controller' => 'Pages', 'action' => 'about']);
    $routes->connect('/markdown', ['controller' => 'Pages', 'action' => 'markdown']);
    $routes->connect('/terms', ['controller' => 'Pages', 'action' => 'terms']);
    $routes->connect('/privacy', ['controller' => 'Pages', 'action' => 'privacy']);
    $routes->connect('/contact', ['controller' => 'Pages', 'action' => 'contact']);
    $routes->connect('/stats', ['controller' => 'Pages', 'action' => 'stats']);

    $routes->connect('/login', ['controller' => 'Users', 'action' => 'login']);
    $routes->connect('/logout', ['controller' => 'Users', 'action' => 'logout']);
    $routes->connect('/register', ['controller' => 'Users', 'action' => 'register']);
    $routes->connect('/forgot-password', ['controller' => 'Users', 'action' => 'forgotPassword']);
    $routes->connect('/reset-password/*', ['controller' => 'Users', 'action' => 'resetPassword']);
    $routes->connect('/settings', ['controller' => 'Users', 'action' => 'settings']);
    $routes->connect('/thinkers', ['controller' => 'Users', 'action' => 'index']);
    $routes->connect('/thinker/:color', ['controller' => 'Users', 'action' => 'view'], ['pass' => ['color']]);

    $routes->connect('/t/:word/*', ['controller' => 'Thoughts', 'action' => 'word'], ['pass' => ['word']]);
    $routes->connect('/questions', ['controller' => 'Thoughts', 'action' => 'questions']);

    $routes->connect('/messages/with/:color', ['controller' => 'Messages', 'action' => 'index'], ['pass' => ['color']]);

    $routes->connect('/color-names', ['controller' => 'Colors', 'action' => 'colorNames']);

    // Bot-catcher
    $botCatcher = ['controller' => 'Pages', 'action' => 'botCatcher'];
    $paths = [
        'addons',
        'admin/editor',
        'admin/SouthidcEditor',
        'admin/start',
        'administrator',
        'advfile',
        'alimail',
        'api',
        'app',
        'apps',
        'archive',
        'archiver',
        'asp.net',
        'auth',
        'back',
        'base',
        'bbs',
        'blog',
        'cgi',
        'ckeditor',
        'ckfinder',
        'clientscript',
        'cms',
        'common',
        'console',
        'coremail',
        'CuteSoft_Client',
        'dialog',
        'docs',
        'editor',
        'examples',
        'extmail',
        'extman',
        'fangmail',
        'FCK',
        'fckeditor',
        'foosun',
        'forum',
        'help',
        'helpnew',
        'home',
        'ids/admin',
        'images',
        'inc',
        'includes',
        'install',
        'issmall',
        'jcms',
        'ks_inc',
        'login',
        'mail',
        'media',
        'new_gb',
        'next',
        'Ntalker',
        'phpmyadmin',
        'plug',
        'plugins',
        'prompt',
        'pub',
        'site',
        'siteserver',
        'skin',
        'system',
        'template',
        'themes',
        'tools',
        'tpl',
        'UserCenter',
        'wcm',
        'web2',
        'weblog',
        'whir_system',
        'wordpress',
        'wp',
        'wp-content',
        'wp-includes',
        'ycportal',
        'ymail',
        'zblog',
        'adminsoft',
    ];
    foreach ($paths as $path) {
        $routes->connect("/$path/*", $botCatcher);
    }
    $files = [
        'admin.php',
        'admin/index.php',
        'admin/login.asp',
        'admin/login.php',
        'app/login.jsp',
        'backup.sql.bz2',
        'bencandy.php',
        'data.sql',
        'db.sql',
        'db.sql.zip',
        'db.tar',
        'dbdump.sql.gz',
        'deptWebsiteAction.do',
        'docs.css',
        'doku.php',
        'dump.gz',
        'dump.sql',
        'dump.tar',
        'dump.tar.gz',
        'e/master/login.aspx',
        'Editor.js',
        'Error.aspx',
        'extern.php',
        'fckeditor.js',
        'feed.asp',
        'history.txt',
        'index.cgi',
        'kindeditor-min.js',
        'kindeditor.js',
        'lang/en.js',
        'License.txt',
        'list.php',
        'maintlogin.jsp',
        'master/login.aspx',
        'mysql.sql',
        'plugin.php',
        'site.sql',
        'sql.sql',
        'sql.tar.gz',
        'temp.sql',
        'User/Login.aspx',
        'wp-cron.php',
        'wp-login.php',
        'Wq_StranJF.js',
        'xmlrpc.php',
        'Search.html',
    ];
    foreach ($files as $file) {
        $routes->connect("/$file", $botCatcher);
    }

    /**
     * Connect catchall routes for all controllers.
     *
     * Using the argument `DashedRoute`, the `fallbacks` method is a shortcut for
     *    `$routes->connect('/:controller', ['action' => 'index'], ['routeClass' => 'DashedRoute']);`
     *    `$routes->connect('/:controller/:action/*', [], ['routeClass' => 'DashedRoute']);`
     *
     * Any route class can be used with this method, such as:
     * - DashedRoute
     * - InflectedRoute
     * - Route
     * - Or your own route class
     *
     * You can remove these routes once you've connected the
     * routes you want in your application.
     */
    $routes->fallbacks(DashedRoute::class);
});

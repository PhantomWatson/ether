<?php
use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;

return static function (RouteBuilder $routes) {
    $routes->setRouteClass(DashedRoute::class);

    $routes->scope('/', function (RouteBuilder $builder) {
        $builder->setExtensions(['json']);

        // Pages
        $builder->connect('/', ['controller' => 'Pages', 'action' => 'home']);
        $builder->connect('/about', ['controller' => 'Pages', 'action' => 'about']);
        $builder->connect('/markdown', ['controller' => 'Pages', 'action' => 'markdown']);
        $builder->connect('/terms', ['controller' => 'Pages', 'action' => 'terms']);
        $builder->connect('/privacy', ['controller' => 'Pages', 'action' => 'privacy']);
        $builder->connect('/contact', ['controller' => 'Pages', 'action' => 'contact']);
        $builder->connect('/stats', ['controller' => 'Pages', 'action' => 'stats']);
        $builder->connect('/maintenance', ['controller' => 'Pages', 'action' => 'maintenanceMode']);

        // Users
        $builder->connect('/login', ['controller' => 'Users', 'action' => 'login']);
        $builder->connect('/logout', ['controller' => 'Users', 'action' => 'logout']);
        $builder->connect('/register', ['controller' => 'Users', 'action' => 'register']);
        $builder->connect('/forgot-password', ['controller' => 'Users', 'action' => 'forgotPassword']);
        $builder->connect('/reset-password/*', ['controller' => 'Users', 'action' => 'resetPassword']);
        $builder->connect('/settings', ['controller' => 'Users', 'action' => 'settings']);
        $builder->connect('/thinkers', ['controller' => 'Users', 'action' => 'index']);
        $builder->connect('/thinker/{color}', ['controller' => 'Users', 'action' => 'view'], ['pass' => ['color']]);
        $builder->connect('/my-profile', ['controller' => 'Users', 'action' => 'myProfile']);

        // Thoughts
        $builder->connect('/t/{word}/*', ['controller' => 'Thoughts', 'action' => 'word'], ['pass' => ['word']]);
        $builder->connect('/questions', ['controller' => 'Thoughts', 'action' => 'questions']);

        // Messages
        $builder->connect('/messages/with/{color}', ['controller' => 'Messages', 'action' => 'index'], ['pass' => ['color']]);

        // Colors
        $builder->connect('/color-names', ['controller' => 'Colors', 'action' => 'colorNames']);

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
            $builder->connect("/$path/*", $botCatcher);
        }

        $files = [
            '.env',
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
            $builder->connect("/$file", $botCatcher);
        }

        $lastParts = [
            '.env',
        ];
        foreach ($lastParts as $lastPart) {
            $builder->connect(
                ":wildcard/$lastPart",
                $botCatcher,
                [
                    'pass' => ['wildcard'],
                    'wildcard' => '.+'
                ]
            );
        }

        $builder->fallbacks();
    });

    $routes->prefix('api', function (RouteBuilder $routes) {
        $routes->fallbacks(DashedRoute::class);
    });
};

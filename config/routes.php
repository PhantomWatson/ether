<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Core\Plugin;
use Cake\Routing\Router;

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
 * If no call is made to `Router::defaultRouteClass`, the class used is
 * `Route` (`Cake\Routing\Route\Route`)
 *
 * Note that `Route` does not do any inflections on URLs which will result in
 * inconsistently cased URLs when used with `:plugin`, `:controller` and
 * `:action` markers.
 *
 */
Router::defaultRouteClass('DashedRoute');

Router::scope('/', function ($routes) {
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

	$routes->connect('/messages/with/:color', ['controller' => 'Messages', 'action' => 'index'], ['pass' => ['color']]);

    /**
     * Connect catchall routes for all controllers.
     *
     * Using the argument `InflectedRoute`, the `fallbacks` method is a shortcut for
     *    `$routes->connect('/:controller', ['action' => 'index'], ['routeClass' => 'InflectedRoute']);`
     *    `$routes->connect('/:controller/:action/*', [], ['routeClass' => 'InflectedRoute']);`
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
    $routes->fallbacks('DashedRoute');
});

/**
 * Load all plugin routes.  See the Plugin documentation on
 * how to customize the loading of plugin routes.
 */
Plugin::routes();

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
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */
	
	Router::connect('/admin/', array('plugin' => 'usermgmt', 'controller' => 'users', 'action' => 'login'));

	App::uses('CakeSession', 'Model/Datasource');
	$dashboard=CakeSession::read('UserAuth.User.dashboard');
	Router::connect('/admin/dashboards', array( 'controller' => 'dashboards','action' => 'dashboard'.$dashboard,'admin'=>false));
	//Router::connect('/admin/dashboards', array( 'controller' => 'dashboards','action' => 'dashboard1','admin'=>false));
	Router::connect('/admin/dashboards2', array( 'controller' => 'dashboards','action' => 'dashboard2','admin'=>false));

	//Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'));
/**
 * ...and connect the rest of 'Pages' controller's URLs.
 */
	Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));

	
/**
 * Load all plugin routes. See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
    CakePlugin::routes();
	
/**
 * Route REST service
 */
	Router::mapResources('api_data_retrives');
	Router::mapResources('api_data164_retrives');
	Router::mapResources('api_data166_retrives');
	Router::mapResources('api_data168_retrives');
	Router::mapResources('api_data1610_retrives');
	Router::mapResources('api_data1611_retrives');
	Router::mapResources('api_data1613_retrives');
	Router::mapResources('api_data170_retrives');
	Router::mapResources('api_data172_retrives');
	Router::mapResources('api_data173_retrives');
	Router::mapResources('api_data174_retrives');
	
	
	
	Router::mapResources('api_data_dist_retrives');
	Router::mapResources('api_data_dist118_retrives');
	Router::mapResources('api_data_dist119_retrives');
	Router::mapResources('api_data_dist120_retrives');
	Router::parseExtensions('json');
	
/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
	require CAKE . 'Config' . DS . 'routes.php';

<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->post('/login', 'UserController::login');
$routes->get('isvalidhash/(:segment)', 'NotificationsController::isValidHash/$1');
$routes->group('api', function ($routes) {
    $routes->post('uploadImage'                , 'ImageController::upload');
    $routes->get('uploads/(:segment)'          , 'ImageController::read/$1');
    $routes->group('user', function ($routes) {
        $routes->get('/'     , 'UserController::index');
        $routes->get('token' , 'UserController::validateJWT');
    });
    $routes->group('rol', function ($routes) {
        $routes->get('/'                , 'RolController::index');
        $routes->get('list'             , 'RolController::list');
        $routes->get('(:num)'           , 'RolController::show/$1');
        $routes->post('create'          , 'RolController::create');
        $routes->post('update'          , 'RolController::update');
        $routes->delete('delete/(:num)' , 'RolController::delete/$1');
        $routes->post('savePermissions' , 'RolController::savePermissions');
    });
    $routes->group('modules', function ($routes) {
        $routes->get('/', 'ModuleController::index');
        $routes->get('submodules', 'ModuleController::getModules');
    });
    $routes->group('formatTypes', function ($routes) {
        $routes->get('/'                , 'FormatTypesController::index');
        $routes->get('list'             , 'FormatTypesController::list');
        $routes->get('(:num)'           , 'FormatTypesController::show/$1');
        $routes->post('create'          , 'FormatTypesController::create');
        $routes->post('update'          , 'FormatTypesController::update');
        $routes->delete('delete/(:num)' , 'FormatTypesController::delete/$1');
    });
    $routes->group('requirementTypes', function ($routes) {
        $routes->get('/'                , 'RequirementTypeController::index');
        $routes->get('list'             , 'RequirementTypeController::list');
        $routes->get('(:num)'           , 'RequirementTypeController::show/$1');
        $routes->post('create'          , 'RequirementTypeController::create');
        $routes->post('update'          , 'RequirementTypeController::update');
        $routes->delete('delete/(:num)' , 'RequirementTypeController::delete/$1');
    });
    $routes->group('category', function ($routes) {
        $routes->get('/'                , 'CategoryController::index');
        $routes->get('list'             , 'CategoryController::list');
        $routes->get('(:num)'           , 'CategoryController::show/$1');
        $routes->post('create'          , 'CategoryController::create');
        $routes->post('update'          , 'CategoryController::update');
        $routes->delete('delete/(:num)' , 'CategoryController::delete/$1');
    });
    $routes->group('notifications', function ($routes) {
        $routes->get('/'                , 'NotificationsController::index');
        $routes->get('list'             , 'NotificationsController::list');
        $routes->get('formValues'       , 'NotificationsController::getFormValues');
        $routes->get('filterValues'     , 'NotificationsController::getFilterValues');
        $routes->post('create'          , 'NotificationsController::createUniqueNotification');
        $routes->post('save'            , 'NotificationsController::saveNotification');
        $routes->post('massive/create'  , 'NotificationsController::createMassiveNotification');
        $routes->post('massive/save'    , 'NotificationsController::saveMassiveNotification');
        $routes->post('changetoarchived', 'NotificationsController::changeToArchived');
        $routes->post('changeToMain'    , 'NotificationsController::changeToMain');
        $routes->post('reply/update'    , 'NotificationsController::setReply');
        $routes->post('changeStatus'    , 'NotificationsController::changeStatus');
        $routes->post('downloadfile'    , 'NotificationsController::downLoadFile');
        $routes->cli('saveOnbase'       , 'NotificationsController::saveDocumentOnbase');
        $routes->post('setstatusreaded/update' , 'NotificationsController::setStatusReaded');
        $routes->group('drafts', function ($routes) {
            $routes->get('list'      , 'NotificationsController::listDrafts');
            $routes->post('delete'   , 'NotificationsController::deleteDrafts');
        });
    });
    $routes->group('reminders', function ($routes) {
        $routes->get('/'       , 'RemindersController::index');
        $routes->get('list'    , 'RemindersController::list');
        $routes->post('update' , 'RemindersController::update');
        $routes->cli('send'    , 'RemindersController::sendReminders');
        
    });
    $routes->group('blockchain', function ($routes) {
        $routes->get('/'       , 'NotificationsExchangeController::index');
        $routes->get('list'    , 'NotificationsExchangeController::list');
        $routes->get('getExcel', 'NotificationsExchangeController::getExcel');
        $routes->post('saveHash/(:segment)/(:num)', 'NotificationsExchangeController::saveHash/$1/$2');

        
    });
    $routes->group('reports', function ($routes) {
        $routes->get('/'                   , 'NotificationsController::index');
        $routes->group('massiveMail', function ($routes) {
            $routes->get('list'         , 'ReportsController::listMail');
            $routes->get('formValues'   , 'NotificationsController::getFormValues');
            $routes->get('filterValues' , 'NotificationsController::getFilterValues');
            $routes->get('entitys'      , 'ReportsController::getEntitys');
            $routes->get('getExcel'     , 'ReportsController::getExcelForReportsMails');
            
        });        
        $routes->group('notifications', function ($routes) {
            $routes->get('list'         , 'ReportsController::list');
            $routes->get('formValues'   , 'NotificationsController::getFormValues');
            $routes->get('filterValues' , 'NotificationsController::getFilterValues');
            $routes->get('campains'     , 'ReportsController::getCampains');
            $routes->get('getExcel'     , 'ReportsController::getExcelForReports');
            
        });        
    });
    $routes->post('uploadImagex' , 'RemindersController::saveImages');
});

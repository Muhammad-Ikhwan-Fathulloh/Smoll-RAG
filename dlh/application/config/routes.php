<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route['default_controller'] = 'frontend/index';

$route['error'] = 'frontend/index/error';

$route['profile'] = 'frontend/profile/index';

$route['registration/new_member'] = 'frontend/registration/new_member';
$route['registration/update_member'] = 'frontend/registration/update_member';
$route['registration/do_insert_new_member'] = 'frontend/registration/do_insert_new_member';
$route['registration/activate_new_member'] = 'frontend/registration/activate_new_member';
$route['registration/success'] = 'frontend/registration/success';

$route['page'] = 'frontend/page';
$route['page/(:any)'] = 'frontend/page/$1';

$route['contact'] = 'frontend/contact';
$route['contact/(:any)'] = 'frontend/contact/$1';

$route['news'] = 'frontend/news';
$route['news/(:any)'] = 'frontend/news/$1';

$route['agenda'] = 'frontend/agenda';
$route['agenda/(:any)'] = 'frontend/agenda/$1';

$route['article'] = 'frontend/article';
$route['article/(:any)'] = 'frontend/article/$1';

$route['download'] = 'frontend/download';

$route['notice'] = 'frontend/notice';
$route['notice/(:any)'] = 'frontend/notice/$1';

$route['member'] = 'frontend/member';
$route['member/(:any)'] = 'frontend/member/$1';

$route['gallery'] = 'frontend/gallery';
$route['gallery/(:any)'] = 'frontend/gallery/$1';

$route['video'] = 'frontend/video';
$route['video/(:any)'] = 'frontend/video/$1';

$route['backend'] = 'backend/index';

$route['404_override'] = '';

/* End of file routes.php */
/* Location: ./application/config/routes.php */
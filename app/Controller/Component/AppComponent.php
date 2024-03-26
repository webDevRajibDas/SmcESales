<?php
class AppComponent extends Component {

	var $components = array('Session', 'Cookie', 'RequestHandler');
	public $uses = array('Usermgmt.UserGroup');
	
	public function menu_permission($controller='',$action='')
	{
		App::import("Model", "Usermgmt.UserGroup");
		$UserGroup = new UserGroup(); 

		$group_id = $this->Session->read('UserAuth.User.user_group_id');
		$permissions = $UserGroup->getPermissions($group_id);
		$access =str_replace(' ','',ucwords(str_replace('_',' ',$controller))).'/'.$action;
		if (in_array($access, $permissions)) {
			return true;
		} 
		return false; 
	}

}

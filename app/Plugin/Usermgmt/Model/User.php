<?php

/*
	This file is part of UserMgmt.

	Author: Chetan Varshney (http://ektasoftwares.com)

	UserMgmt is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	UserMgmt is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
*/
App::uses('UserMgmtAppModel', 'Usermgmt.Model');
App::uses('CakeEmail', 'Network/Email');
class User extends UserMgmtAppModel {

	// data filter
	public function filter($params, $conditions) {
		$conditions = array();
		if(CakeSession::read('Office.parent_office_id') != 0)
		{
			$conditions[] = array('SalesPerson.office_id' => CakeSession::read('Office.id'));
		}
		if (!empty($params['SalesPerson.office_id'])) {
            $conditions[] = array('SalesPerson.office_id' => $params['SalesPerson.office_id']);
        }
		if (!empty($params['User.user_group_id'])) {
            $conditions[] = array('User.user_group_id' => $params['User.user_group_id']);
        }
		if (!empty($params['User.username'])) {
            $conditions[] = array('User.username' => $params['User.username']);
        }
        if (!empty($params['User.mac_id'])) {
            $conditions[] = array('User.mac_id' => $params['User.mac_id']);
        }
        if (!empty($params['User.version'])) {
            $conditions[] = array('User.version' => $params['User.version']);
        }
        return $conditions;
    } 
	
	/**
	 * This model belongs to following models
	 *
	 * @var array
	 */
	var $belongsTo = array(
		'Usermgmt.UserGroup',
		'SalesPerson' => array(
			'className' => 'SalesPerson',
			'foreignKey' => 'sales_person_id',
			'dependent' => true
		)
	);
	/**
	 * This model has following models
	 *
	 * @var array
	 */
	var $hasMany = array('LoginToken'=>array('className'=>'Usermgmt.LoginToken','limit' =>1));
	/**
	 * model validation array
	 *
	 * @var array
	 */
	//var $validate = array();
	/**
	 * model validation array
	 *
	 * @var array
	 */
	function LoginValidate() {
		$validate1 = array(
				'email'=> array(
					'mustNotEmpty'=>array(
						'rule' => 'notEmpty',
						'message'=> 'Please enter email or username')
					),
				'password'=>array(
					'mustNotEmpty'=>array(
						'rule' => 'notEmpty',
						'message'=> 'Please enter password')
					)
			);
		$this->validate=$validate1;
		return $this->validates();
	}
	/**
	 * model validation array
	 *
	 * @var array
	 */
	
	public $validate = array(
			"user_group_id" => array(
				'rule' => array('comparison', '!=', 0),
				'message'=> 'Please select group'),
				
			'username'=> array(
				'mustNotEmpty'=>array(
					'rule' => 'notEmpty',
					'message'=> 'Please enter username',
					'last'=>true),
				'isUnique' => array(
					'rule' => 'isUnique',
					'message'=> 'Username already exists.'
				),
				'mustalphanumeric'=>array(
					'rule' =>'alphanumeric',
					'message' =>'Special character not allowed.',
				'last'=>true),
				'mustBeLonger'=>array(
					'rule' => array('minLength', 4),
					'message'=> 'Username must be greater than 3 characters',
					'last'=>true)
			),
			
			'password'=>array(
				'mustNotEmpty'=>array(
					'rule' => 'notEmpty',
					'message'=> 'Please enter password',
					'on' => 'create',
					'last'=>true),
				'mustBeLonger'=>array(
					'rule' => array('minLength', 4),
					'message'=> 'Password must be greater than 3 characters',
					'on' => 'create',
					'last'=>true)
			),
			'cpassword'=>array(
				'mustNotEmpty'=>array(
					'rule' => 'notEmpty',
					'message'=> 'Please enter Confirm password',
					'on' => 'create',
					'last'=>true
				),
				'compare'=>array(
					'rule' => array('validate_passwords'),
					'message' => 'Both passwords must match',
					'on' => 'create'
				)					
			),
			'old_password'=>array(
				'required'=>array(
					'rule'=>array('password_verifies'),
					'message'=>'Icorrect Old Password',
					'on' => 'create'
					)
				)
			);
		
	
	public function alphanumeric() 
	{
		$username = $this->data['User']['username'];
		if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $username))
		{
			return FALSE;
		}else{
			return TRUE;
		}
	}
	public function password_verifies() {
		/*pr($this->data);*/
    // getting password via field method by assuming you're setting $this->User->id from your controller
    /*echo UserAuthComponent::makePassword($this->data[$this->alias]['old_password']);
    echo '<br>db'.$this->field('password',array('id'=>$this->data[$this->alias]['id'])); exit;*/
		return UserAuthComponent::makePassword($this->data[$this->alias]['old_password']) == $this->field('password',array('id'=>$this->data[$this->alias]['user_id'])); 
	}

	/**
	 * Used to match passwords
	 *
	 * @access protected
	 * @return boolean
	 */
	
	public function validate_passwords() {
		return ($this->data['User']['password']===$this->data['User']['cpassword']);
	}
	/**
	 * Used to send registration mail to user
	 *
	 * @access public
	 * @param array $user user detail array
	 * @return void
	 */
	public function sendRegistrationMail($user) {
		// send email to newly created user
		$userId=$user['User']['id'];
		$email = new CakeEmail();
		$fromConfig = emailFromAddress;
		$fromNameConfig = emailFromName;
		$email->from(array( $fromConfig => $fromNameConfig));
		$email->sender(array( $fromConfig => $fromNameConfig));
		$email->to($user['User']['email']);
		$email->subject('Your registration is complete');
		//$email->transport('Debug');
		$body="Welcome ".$user['User']['name'].", Thank you for your registration on ".SITE_URL." \n\n Thanks,\n".emailFromName;
		try{
			$result = $email->send($body);
		} catch (Exception $ex) {
			// we could not send the email, ignore it
			$result="Could not send registration email to userid-".$userId;
		}
		$this->log($result, LOG_DEBUG);
	}
	/**
	 * Used to send email verification mail to user
	 *
	 * @access public
	 * @param array $user user detail array
	 * @return void
	 */
	public function sendVerificationMail($user) {
		$userId=$user['User']['id'];
		$email = new CakeEmail();
		$fromConfig = emailFromAddress;
		$fromNameConfig = emailFromName;
		$email->from(array( $fromConfig => $fromNameConfig));
		$email->sender(array( $fromConfig => $fromNameConfig));
		$email->to($user['User']['email']);
		$email->subject('Email Verification Mail');
		$activate_key = $this->getActivationKey($user['User']['password']);
		$link = Router::url("/userVerification?ident=$userId&activate=$activate_key",true);
		$body="Hi ".$user['User']['name'].", Click the link below to complete your registration \n\n ".$link;
		try{
			$result = $email->send($body);
		} catch (Exception $ex){
			// we could not send the email, ignore it
			$result="Could not send verification email to userid-".$userId;
		}
		$this->log($result, LOG_DEBUG);
	}
	/**
	 * Used to generate activation key
	 *
	 * @access public
	 * @param string $password user password
	 * @return hash
	 */
	public function getActivationKey($password) {
		$salt = Configure::read ( "Security.salt" );
		return md5(md5($password).$salt);
	}
	/**
	 * Used to send forgot password mail to user
	 *
	 * @access public
	 * @param array $user user detail
	 * @return void
	 */
	public function forgotPassword($user) {
		$userId=$user['User']['id'];
		$email = new CakeEmail();
		$fromConfig = emailFromAddress;
		$fromNameConfig = emailFromName;
		$email->from(array( $fromConfig => $fromNameConfig));
		$email->sender(array( $fromConfig => $fromNameConfig));
		$email->to($user['User']['email']);
		$email->subject(emailFromName.': Request to Reset Your Password');
		$activate_key = $this->getActivationKey($user['User']['password']);
		$link = Router::url("/activatePassword?ident=$userId&activate=$activate_key",true);
		$body= "Welcome ".$user['User']['name'].", let's help you get signed in

You have requested to have your password reset on ".emailFromName.". Please click the link below to reset your password now :

".$link."


If above link does not work please copy and paste the URL link (above) into your browser address bar to get to the Page to reset password

Choose a password you can remember and please keep it secure.

Thanks,\n".

emailFromName;
		try{
			$result = $email->send($body);
		} catch (Exception $ex){
			// we could not send the email, ignore it
			$result="Could not send forgot password email to userid-".$userId;
		}
		$this->log($result, LOG_DEBUG);
	}
	/**
	 * Used to mark cookie used
	 *
	 * @access public
	 * @param string $type
	 * @param string $credentials
	 * @return array
	 */
	public function authsomeLogin($type, $credentials = array()) {
		switch ($type) {
			case 'guest':
				// You can return any non-null value here, if you don't
				// have a guest account, just return an empty array
				return array();
			case 'cookie':
				list($token, $userId) = split(':', $credentials['token']);
				$duration = $credentials['duration'];

				$loginToken = $this->LoginToken->find('first', array(
					'conditions' => array(
						'user_id' => $userId,
						'token' => $token,
						'duration' => $duration,
						'used' => false,
						'expires <=' => date('Y-m-d H:i:s', strtotime($duration)),
					),
					'contain' => false
				));
				if (!$loginToken) {
					return false;
				}
				$loginToken['LoginToken']['used'] = true;
				$this->LoginToken->save($loginToken);

				$conditions = array(
					'User.id' => $loginToken['LoginToken']['user_id']
				);
			break;
			default:
				return array();
		}
		return $this->find('first', compact('conditions'));
	}
	/**
	 * Used to generate cookie token
	 *
	 * @access public
	 * @param integer $userId user id
	 * @param string $duration cookie persist life time
	 * @return string
	 */
	public function authsomePersist($userId, $duration) {
		$token = md5(uniqid(mt_rand(), true));
		$this->LoginToken->create(array(
			'user_id' => $userId,
			'token' => $token,
			'duration' => $duration,
			'expires' => date('Y-m-d H:i:s', strtotime($duration)),
		));
		$this->LoginToken->save();
		return "${token}:${userId}";
	}
	/**
	 * Used to get name by user id
	 *
	 * @access public
	 * @param integer $userId user id
	 * @return string
	 */
	public function getNameById($userId) {
		$res = $this->findById($userId);
		$name=(!empty($res)) ? $res['User']['name'] : '';
		return $name;
	}
}

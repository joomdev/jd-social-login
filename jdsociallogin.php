<?php

/**
 * @package   JD Social Login
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2019 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

require_once dirname(__FILE__) . '/lib/src/autoload.php';
use Hybridauth\Hybridauth;
use Hybridauth\HttpClient;
use Hybridauth\Storage\Session;
defined('_JEXEC') or die;
/**
 *  system plugin
 */

class plgSystemJDsociallogin extends JPlugin {
	protected $app;
	protected $app_id;
	/**
	 * The Facebook App ID. Default: APP ID
	 *
	 * @var   Int
	 */
	
	protected $app_secret;
	/**
	 * The Facebook App Secret. Default: APP SECRET
	 *
	 * @var   Int
	 */
	/**
	 * Are the substitutions enabled?
	 *
	 * @var   bool
	 */
	private $enabled = true;
	/**
	 * The names of the login modules to intercept. Default: mod_login
	 *
	 * @var   array
	 */
	private $loginModules = array('mod_login','mod_registerlogin');

	/**
	 * Should I link the login page of com_users and add social login buttons there?
	 *
	 * @var   bool
	 */
	private $includeLogin = true;

	public $config= [];
	public $data= [];

	public $provider;
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);

		// Am I enabled?
		$this->enabled = $this->isEnabled();

		if (!$this->enabled)
		{
			return;
		}
		// Load the other plugin parameters
		$this->includeLogin       = $this->params->get('allowInUserPage', 1);
	}
	/**
	 * The Function Which Will place the Social Buttons on Login Module
	 *
	 * @var   array
	 */
	public function onRenderModule(&$module, &$attribs)
	{
		if (!$this->enabled)
		{
			return;
		}
		$app = JFactory::getApplication();
		
      // Frontend only, otherwise return.
      if(!$app->isClient('site'))
      {
         return false;
		}
		static $docType = null;

		if (is_null($docType))
		{
			try
			{
				$document = JFactory::getApplication()->getDocument();
			}
			catch (Exception $e)
			{
				$document = null;
			}

			$docType = (is_null($document)) ? 'error' : $document->getType();

			if ($docType != 'html')
			{
				$this->enabled = false;

				return;
			}
		}

		// If it's not a module I need to intercept bail out
		if (!in_array($module->module, $this->loginModules))
		{
			return;
		}

		$socialLoginButtons = $this->Getstyle($this->params->get('socialOrder',"facebook"),$this->params->get('enableFb'),$this->params->get('enableTwitter'));
		$module->content    .= $socialLoginButtons;
	}
	
	public function GetStyle($style,$fb,$twitter){
		$facebookSocailBtn="";
		$twitterSocialBtn="";
		$fbText="";
		$twitterText="";
		$buttonsStyle = $this->params->get("buttonsStyle",'');

		if($buttonsStyle =="large-buttons"){
			$fbText = JTEXT::_("Facebook");
			$twitterText = JTEXT::_("Twitter");
		}
		  if($fb){	
				$facebookSocailBtn = '
										<div class="social-form">
											<form  action="" method="get" id="" name="">
												<div class="jd-button-control">
												<button type="submit" class="loginBtn loginBtn--facebook">
													<span class="social-icon">
														<img src="'.JURI::root().'/media/plg_jdsociallogin/assets/img/facebook.png" width="33"></img>
													</span>'.$fbText.'</button>
													<input type="hidden" value="sociallogin" name="com_ajax">
													<input type="hidden" value="login" name="action">
													<input type="hidden" value="Facebook" name="provider">
												</div>
											</form>
										</div>';
		  }						
										
		if($twitter){								
				$twitterSocialBtn   = '
									<div class="social-form">
										<form  action="" method="get" id="" name="">
											<div class="jd-button-control">
												<button type="submit" class="loginBtn loginBtn--twitter">
												<span class="social-icon">
													<img src="'.JURI::root().'/media/plg_jdsociallogin/assets/img/twitter.png" width="33"></img>
												</span>'.$twitterText.'</button>
												<input type="hidden" value="sociallogin" name="com_ajax">
												<input type="hidden" value="login" name="action">
												<input type="hidden" value="Twitter" name="provider">
											</div>
										</form>
									</div>';
		}			
		
			    
	    if($style=="facebook"){
	   	return '<div class="jd-social-btns"><p>'.JTEXT::_('OR').'</p>'.$facebookSocailBtn.$twitterSocialBtn.'</div>';
	    }else{
	   	return '<div class="jd-social-btns"><p>'.JTEXT::_('OR').'</p>'.$twitterSocialBtn.$facebookSocailBtn.'</div>';
	    }   
	}

	/**
	 * Should I enable the substitutions performed by this plugin?
	 *
	 * @return  bool
	 */
	private function isEnabled()
	{
		// It only make sense to let people log in when they are not already logged in ;)
		if (!JFactory::getUser()->guest)
		{
			return false;
		}

		return true;
	}

   public function onAfterInitialise(){
      $app = JFactory::getApplication();
		
      // Frontend only, otherwise return.
      if(!$app->isClient('site'))
      {
         return false;
		}


		// Getting Social Media Params
		$this->config['fb_app_id']  				=   $this->params->get('fb_app_id');
		$this->config['fb_app_secret']   		=   $this->params->get('fb_app_secret');
		$this->config['twitter_app_id']   		=   $this->params->get('twitter_app_id');
		$this->config['twitter_app_secret']    =   $this->params->get('twitter_app_secret');
     
      $jinput 					=  JFactory::getApplication()->input;
      $com_ajax 				=  $jinput->get('com_ajax', '', '');
	  	$this->config['provider'] 		=  $jinput->get('provider', '', '');
	  
      $provider				=  $jinput->get('provider', '', '');
		$callback 				=  $jinput->get('callback', '', '');
		$oauth_token 			=  $jinput->get('oauth_token', '', '');
		$code 					=  $jinput->get('code', '', '');

      if($com_ajax=="sociallogin" && $provider =="Twitter"){
         $this->DoActivity();
		}else if($com_ajax=="sociallogin" && $provider =="Facebook")
		{  $this->DoActivity();
		}else if($callback =="true" ){
			if($code){
				$this->config['provider'] ="Facebook";
			}else if($oauth_token){
				$this->config['provider'] ="Twitter";
			}
			$this->DoActivity();
      }
   } 
   public function DoActivity(){
		$useInfo= $this->Auth($this->config); 
		if(isset($useInfo) && !empty($useInfo->email)){
			$userData = $this->CheckUserByEmail($useInfo);
			if($userData){
				$this->Userlogin($userData);
				$return = $this->getReturnUrl($this->params->get('login',408));
				JFactory::getApplication()->redirect(base64_decode($return));
			}else{
				$user  = $this->RegisterUser($useInfo);
			
				$this->insertUserProfileData($user->id,"jdsociallogin.".$this->config['provider'],$useInfo->identifier);
				$this->Userlogin($user);
				$return = $this->getReturnUrl($this->params->get('login'));
				JFactory::getApplication()->redirect(base64_decode($return));
			}
		}else{
			JFactory::getApplication()->enqueueMessage("", 'error');
		}
   }   

	public static function insertUserProfileData($userId, $slug,$identifier)
	{
		$userProfileData = [
			'userid' => $identifier,
			'token'  => json_encode(JSession::getFormToken()),
		];
		
		$db = JFactory::getDbo();
		$insertData = [];
		foreach ($userProfileData as $key => $value)
		{
			$insertData[] = $db->q($userId) . ', ' . $db->q($slug . '.' . $key) . ', ' . $db->q($value);
		}

		$query = $db->getQuery(true)
			->insert($db->qn('#__user_profiles'))
			->columns($db->qn('user_id') . ', ' . $db->qn('profile_key') . ', ' . $db->qn('profile_value'))
			->values($insertData);
		$db->setQuery($query)->execute();
	}

   public static function Auth($app_config){
      $config = [
         'callback' => JURI::ROOT()."?plugin=sociallogin&callback=true",
         'providers' => [
             'Facebook' => [ 
                 'enabled' => true,
                 'keys'    => [ 'id' =>$app_config['fb_app_id'], 'secret' => $app_config['fb_app_secret'] ],
				 ],
				 'Twitter' => [ 
					'enabled' => true,
					'keys'    => [ 'key' =>$app_config['twitter_app_id'], 'secret' => $app_config['twitter_app_secret'] ],
					"includeEmail" => true,
			  ]
         ],
      ];

      try {    
         $hybridauth = new Hybridauth($config);
         $adapter = $hybridauth->authenticate( $app_config['provider'] );
         $accessToken = $adapter->getAccessToken();
         $adapter->setAccessToken($accessToken);
			$userProfile = $adapter->getUserProfile();
			
         return  $userProfile;
         $adapter->disconnect();
      }
      catch (\Exception $e) {
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
      }

	}
	
	/**
	 * The Function Which Will place the Social Buttons on com_user view login .
	 *
	 * @return  void
	 */
	public function onAfterDispatch()
	{

		// Are we enabled?
		if (!$this->includeLogin )
		{
			return;
		}


		// Make sure I can get basic information
		try
		{
			$app     = JFactory::getApplication();
			$user    = JFactory::getUser();
			$input   = $app->input;
		}
		catch (Exception $e)
		{
			return;
		}

		// No point showing a login button when you're already logged in
		if (!$user->guest)
		{
			return;
		}

		// Make sure this is the Users component
		$option = $input->getCmd('option');

		if ($option !== 'com_users')
		{
			return;
		}

		// Make sure it is the right view / task
		$view = $input->getCmd('view');
		$task = $input->getCmd('task');

		$check1 = is_null($view) && is_null($task);
		$check2 = is_null($view) && ($task === 'login');
		$check3 = ($view === 'login') && is_null($task);

		if (!$check1 && !$check2 && !$check3)
		{
			return;
		}

		// Make sure it's an HTML document
		$document = $app->getDocument();

		if ($document->getType() != 'html')
		{
			return;
		}

		// Get the component output and append our buttons
		$buttons = $this->Getstyle($this->params->get('socialOrder',"facebook"),$this->params->get('enableFb',1),$this->params->get('enableTwitter',1));

		$buffer          = $document->getBuffer();

		$componentOutput = $buffer['component'][''][''];
		$componentOutput .= $buttons;
		$document->setBuffer($componentOutput, 'component');
	}


	public static function CheckUserByEmail($user){
             
      $db = JFactory::getDbo();
      $query = $db->getQuery(true);
      $query = 'SELECT * FROM #__users WHERE email = ' . $db->Quote($user->email);
      $db->setQuery($query, 0, 1);
		$result = $db->loadObject();
      if($result){
       return  $result;
      }else{
         return  $result;
      }
	}
   public static function Userlogin($user){
		 
      $result = JFactory::getApplication()->login(array('username'=>$user->username,'password'=>$user->email)); 
		if(isset($result) && !empty($result)){
			$response['success']				=	true;
         $response['success_message']	=  JText::_('MOD_REGISTERLOGIN_LOGIN_SUCCESS');
		}else{
			$response['error']			=	true;
			$response['error_message']	= 	JText::_('MOD_REGISTERLOGIN_WRONG_LOGIN_MESSAGE');
		}
		return $response;
	}
	
	public static function generateUsername($displayName){
		$username = str_replace(" ",".",strtolower($displayName));
		return $username;
	}
	public static function getReturnUrl($menuitem)
	{
		$app  = JFactory::getApplication();
		$item = $app->getMenu()->getItem($menuitem);
		// Stay on the same page
		$url = JUri::getInstance()->toString();
		if ($item)
		{
			$lang = '';
			if ($item->language !== '*' && JLanguageMultilang::isEnabled())
			{
				$lang = '&lang=' . $item->language;
			}
			$url = JURI::root().'index.php?Itemid=' . $item->id . $lang;
		}
		return base64_encode($url);
	}
	public static function getType()
	{
		$user = JFactory::getUser();
		return (!$user->get('guest')) ? 'logout' : 'login';
	}
   public static function RegisterUser($userDetails){

      $language = JFactory::getLanguage();
		$language->load('com_users');	
		$userParams    =  JComponentHelper::getParams('com_users');
		$user 			= new JUser;
	
      JPluginHelper::importPlugin('user');
      $data['name']        =  $userDetails->displayName; 
      $data['username']    =  plgSystemJDsociallogin::generateUsername($userDetails->displayName); 
		$data['email'] 		=  JStringPunycode::emailToPunycode($userDetails->email);
		$data['password'] 	=  $userDetails->email;  //JUserHelper::genRandomPassword();
		$data['activation']  =  0;
		$data['block']			=  0;
		
		// Bind the data.
		if (!$user->bind($data)){
		}
	
		// Store the data.
		if (!$user->save())
		{
			if($user->getError() == 'Username in use.'){
				$errorMessage  = JText::_('COM_USERS_REGISTER_USERNAME_MESSAGE');
			}else{
				$errorMessage  = $user->getError();
			}
			$data['message']  = $errorMessage;
			$msg = $data['message'];
			$data['success']  = false;
			return $msg;
			exit;
		}else{
			$id 		= $user->id;
			$gId 		= $userParams->get('new_usertype');
			$db    	= JFactory::getDBO();
			$query 	= $db->getQuery(true);
			$columns = array('user_id', 'group_id');
			$values 	= array($id,$gId);
			$query
				->insert($db->quoteName('#__user_usergroup_map'))
				->columns($db->quoteName($columns))
				->values(implode(',', $values));
			$db->setQuery($query);
			$db->execute();
			$config = JFactory::getConfig();
			$query = $db->getQuery(true);

			// Compile the notification mail values.
			$data = $user->getProperties();
			$data['fromname'] = $config->get('fromname');
			$data['mailfrom'] = $config->get('mailfrom');
			$data['sitename'] = $config->get('sitename');
			$data['siteurl']  = str_replace('plugin/plg_jdsociallogin/','',JURI::root());

			// Handle account activation/confirmation emails.
	
				$emailSubject = JText::sprintf(
					'COM_USERS_EMAIL_ACCOUNT_DETAILS',
					$data['name'],
					$data['sitename'] );

			
				$emailBody = JText::sprintf(
						'COM_USERS_EMAIL_REGISTERED_BODY_NOPW',
						$data['name'],
						$data['sitename'],
						$data['siteurl'] );
				
			
			try{
			// Send the registration email.
			$return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $data['email'], $emailSubject, $emailBody);
			}
			catch(Exception $e){
               echo $e;
			}
			return $user;
			exit;
		}
   }
   public function onBeforeCompileHead() {
      if($this->app->isSite()){
         $docuemnt = \JFactory::getDocument();
         $docuemnt->addStyleSheet(JURI::root() . 'media/plg_jdsociallogin/assets/css/styles.css');
         $docuemnt->addScript(JURI::root() . 'media/plg_jdsociallogin/assets/js/script.js');
      }
	}
}
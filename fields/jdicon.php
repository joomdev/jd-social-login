<?php
/**
 * @package   Mod_jdtemplate  
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2018 Joomdev, Inc. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('JPATH_PLATFORM') or die;


/**
 * @link   http://www.w3.org/TR/html-markup/command.radio.html#command.radio
 * @since  11.1
 */
 
 
jimport('joomla.form.formfield');
class JFormFieldJdIcon extends JFormField
{
    
    /**
     * The form field type.
     *
     * @var    string
     * @since  11.1
     */
    protected $type = 'jdicon';
    
    /**
     * Name of the layout being used to render the field
     *
     * @var    string
     * @since  3.5
     */
    
    /**
     * Method to get the radio button field input markup.
     *
     * @return  string  The field input markup.
     *
     * @since   11.1
     */
    protected function getInput()
    {
        $renderer = new JLayoutFile('jdicon',JPATH_ROOT.'/plugins/system/jdsociallogin/layouts');
		  $data = $this->getLayoutData();
		  $extraData = array(
			  'value' => $this->value,
			  'fieldname' => $this->fieldname,
		  );  
      $data = array_merge($data, $extraData);
      return $renderer->render($data);
    }
  
    
}
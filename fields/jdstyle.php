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
class JFormFieldJdstyle extends JFormField
{
    
    /**
     * The form field type.
     *
     * @var    string
     * @since  11.1
     */
    protected $type = 'jdstyle';
    
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
        $avatars = $this->getOptions();
        $renderer = new JLayoutFile('jdstyle',JPATH_ROOT.'/plugins/system/jdsociallogin/layouts');
        return $renderer->render(['avatars' => $avatars, 'field' => $this]);
    }

    public function getOptions() {
        $dir = JPATH_SITE . '/media/plg_jdsociallogin/assets/img/style';
        $files = array();
        foreach (glob($dir . "/*.png") as $file) {
           $files[] = $file;
        }
        return $files;
     }
  
    
}
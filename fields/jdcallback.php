<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

//jimport('joomla.form.formfield');

class JFormFieldJDCallback extends JFormField {

   protected $type = 'jdcallback';

   public function getInput() {
   
      return '<span style="background: #e8edf1;padding: 5px; font-size: 15px; border-radius: 2px;">'.JURI::ROOT().JTEXT::_($this->element['url']).'</span>';
   } 
}
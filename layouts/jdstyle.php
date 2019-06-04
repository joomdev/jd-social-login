<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
extract($displayData);

$document = JFactory::getDocument();
$style = '.jd-avatar-input label{'
        . 'display: inline-block;margin-right: 4px;'
        . '}'
        . '.jd-avatar-input label input[type=radio]:checked ~ img{box-shadow: 0 0 0 3px #00a0d2;border-radius: 3px;'
        . '.jd-avatar-input label img{opacity: 0.5}';
$document->addStyleDeclaration($style);
?>

<div class="jd-avatar-input">
   <?php
   foreach ($avatars as $avatar) {
      $pathinfo = pathinfo($avatar);
      ?>
      <label>
         <input style="display:none" type="radio" name="<?php echo $field->name; ?>" value="<?php echo $pathinfo['filename']; ?>" <?php echo ($field->value == $pathinfo['filename'] ? 'checked' : ''); ?> />
         <img width="223" src="<?php echo JURI::root(); ?>media/plg_jdsociallogin/assets/img/style/<?php echo $pathinfo['filename']; ?>.png" />
      </label>
   <?php } ?>
</div>
<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_zekrshomar
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$zekr = $this->item->id;
$user = $this->params->get('user_id');

$document = JFactory::getDocument();

$menu     = JFactory::getApplication()->getMenu();
$menuItem = $menu->getItems( 'link', 'index.php?option=com_zekrshomar&view=receivecode', true );
$Itemid   = $menuItem->id ? '&Itemid='.$menuItem->id : '';

// Add Script
$script = '// Ajax Update Zekr Number
function ajaxZekr(num)
{
	if(num && ' . $zekr . ' && ' . $user . ')
	{
		jQuery.ajax(
		{
			dataType: "json",
			type: "POST",
			url:"' . JURI::base(true) . '/index.php?option=com_zekrshomar&task=ajaxZekr",
			data: {
				zekr_id: ' . $zekr . ',
				user_id: ' . $user . ',
				number: num,
			},
			success:function(result)
			{
				jQuery("#usertotal").html(result["usertotal"]);
				jQuery("#total").html(result["total"]);
			}
		});
	}
}';
$document->addScriptDeclaration($script);

// Add styles
$bg_color   = $this->params->get('bg_color');
$font_color = $this->params->get('font_color');

if((JRequest::getString("tmpl") == 'component') && ($bg_color || $font_color))
{
	$style = 'body{';

	if($bg_color)
	{
		$style .= 'background-color: ' . $bg_color . ';';
	}

	if($font_color)
	{
		$style .= 'color: ' . $font_color . ';';
	}

	$style .= '}'; 

	$document->addStyleDeclaration($style);
}

// Get some params
$number_style   = $this->params->get('number_style');
$options_style  = $this->params->get('options_style');
$options_values = explode('-', $this->params->get('options_values', '5-10-15-20-50-100'));

?>

<div class="zekr<?php echo $this->pageclass_sfx; ?>">
	<?php if ($this->params->get('page_heading')) : ?>
	<h4> <?php echo $this->escape($this->params->get('page_heading')); ?> </h4>
	<?php endif; ?>
	<br />
	<div class="mention text-center">
		<?php if (in_array('single', $number_style)) : ?>
		<a class="btn" onclick="ajaxZekr(1);">
		<?php endif; ?>
		<?php if (($this->params->get('mention_style') == 'image') && $this->item->image) : ?>
		<img src="<?php echo $this->item->image; ?>" alt="<?php echo $this->escape($this->item->mention); ?>" />
		<?php else: ?>
		<?php echo $this->escape($this->item->mention); ?>
		<?php endif; ?>
		<?php if (in_array('single', $number_style)) : ?>
		</a>
		<?php endif; ?>
	</div>
	<?php if (in_array('options', $number_style)) : ?>
	<br />
	<div class="options_number text-center">
	<?php if ($options_style == 'list') : ?>
		<select name="options_number" class="input-small">
			<option value=""><?php echo JText::_('COM_ZEKRSHOMAR_SELECT_NUMBER'); ?></option>
			<?php foreach($options_values as $value) : ?>
			<option value="<?php echo $value; ?>" onclick="ajaxZekr(<?php echo $value; ?>);"><?php echo $value; ?></option>
			<?php endforeach; ?>
		</select>
	<?php elseif($options_style == 'radio'): ?>
		<?php foreach($options_values as $value) : ?>
		<label class="radio inline">
			<input name="options_number" type="radio" onclick="ajaxZekr(<?php echo $value; ?>);" value="<?php echo $value; ?>" /> <?php echo $value; ?>
		</label>
		<?php endforeach; ?>
	<?php else: ?>
		<?php foreach($options_values as $value) : ?>
		<input name="options_number" type="button" class="btn" onclick="ajaxZekr(<?php echo $value; ?>);" value="<?php echo $value; ?>" />
		<?php endforeach; ?>
	<?php endif; ?>
	</div>
	<?php endif; ?>
	<?php if (in_array('custom', $number_style)) : ?>
	<br />
	<div class="custom_number text-center">
		<div class="input-append">
			<input class="text-center input-mini" id="custom_number" type="text" placeholder="<?php echo JText::_('COM_ZEKRSHOMAR_NUMBER'); ?>" />
			<button type="button" class="btn" onclick="ajaxZekr(jQuery('#custom_number').val());"><?php echo JText::_('COM_ZEKRSHOMAR_SEND'); ?></button>
		</div>
	</div>
	<?php endif; ?>
	<?php if($this->item->sumnumber) : ?>
	<div class="total text-center"><?php echo JText::_('COM_ZEKRSHOMAR_TOTAL'); ?>: <strong id="total"><?php echo $this->item->sumnumber; ?></strong></div>
	<?php endif; ?>
	<?php if(isset($this->item->usernumber)) : ?>
	<div class="total text-center"><?php echo JText::_('COM_ZEKRSHOMAR_USER_TOTAL'); ?>: <strong id="usertotal"><?php echo $this->item->usernumber; ?></strong></div>
	<?php endif; ?>
	<br />
	<div class="text-center">
		<a href="<?php echo JRoute::_('index.php?option=com_zekrshomar&view=receivecode'.$Itemid); ?>" title="<?php echo JText::_('COM_ZEKRSHOMAR_RECEIVE_CODE'); ?>" target="_blank"><?php echo JText::_('COM_ZEKRSHOMAR_RECEIVE_CODE'); ?></a>
	</div>
</div>

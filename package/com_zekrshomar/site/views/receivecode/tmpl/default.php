<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_zekrshomar
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('bootstrap.framework');
JHtml::_('behavior.formvalidation');

$document = JFactory::getDocument();

// Add Script
$script = 'jQuery(document).ready(function()
{
	jQuery("#previewBtn").click(function()
	{
		if(document.formvalidator.isValid(document.id("receiveCodeForm")))
		{
			jQuery("#previewModal").modal("toggle");
		}
	})

	jQuery("#previewModal").on("show", function()
	{
		width = jQuery("#jform_iframe_width").val();
		height = jQuery("#jform_iframe_height").val();
		if(!width) width = "100%";
		if(!height) height = "200";
		url = "index.php?" + jQuery("#receiveCodeForm").serialize();
		htmlcode = "<div class=\"text-center\"><iframe src=\"" + url + "\" width=\"" + width + "\" height=\"" + height + "\" scrolling=\"Auto\"></iframe></div>";
		jQuery("#previewModal .modal-body").html(htmlcode)
	})

	jQuery("#receiveCodeBtn").click(function()
	{
		width = jQuery("#jform_iframe_width").val();
		height = jQuery("#jform_iframe_height").val();
		if(!width) width = "100%";
		if(!height) height = "200";
		url = "' . JUri::base() . 'index.php?" + jQuery("#receiveCodeForm").serialize();
		htmlcode = "<pre class=\"ltr\">&ltiframe src=\"" + url + "\" width=\"" + width + "\" height=\"" + height + "\" scrolling=\"Auto\" frameborder=\"0\"&gt&lt/iframe&gt</pre>";
		jQuery("#previewModal .modal-body").html(htmlcode);
	})
})';
$document->addScriptDeclaration($script);

?>

<div class="receivecode<?php echo $this->pageclass_sfx; ?>">
	<?php if ($this->params->get('page_heading')) : ?>
	<div class="page-header span12">
		<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	</div>
	<?php endif; ?>
	<form name="receiveCodeForm" id="receiveCodeForm" class="form-validate form-horizontal">
		<div class="row-fluid">
			<div class="span6">
				<?php echo $this->form->getControlGroup('id'); ?>
				<?php echo $this->form->getControlGroup('mention_style'); ?>
				<?php echo $this->form->getControlGroup('number_style'); ?>
				<?php echo $this->form->getControlGroup('options_style'); ?>
				<?php echo $this->form->getControlGroup('options_values'); ?>
			</div>
			<div class="span6">
				<?php echo $this->form->getControlGroup('bg_color'); ?>
				<?php echo $this->form->getControlGroup('font_color'); ?>
				<?php echo $this->form->getControlGroup('iframe_width'); ?>
				<?php echo $this->form->getControlGroup('iframe_height'); ?>
				<?php echo $this->form->getControlGroup('email'); ?>
			</div>
		</div>
		<div class="form-actions"><a id="previewBtn" href="#previewModal" role="button" class="btn btn-primary"><?php echo JText::_('COM_ZEKRSHOMAR_PREVIEW'); ?></a></div>
		<input type="hidden" name="option" value="com_zekrshomar" />
		<input type="hidden" name="view" value="zekr" />
		<input type="hidden" name="tmpl" value="component" />
		<input type="hidden" name="lang" value="fa" />
		<input type="hidden" name="user_id" value="<?php echo JFactory::getUser()->id; ?>" />
	</form>
	<!-- Preview Modal -->
	<div id="previewModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<h3 id="previewModalLabel"><?php echo JText::_('COM_ZEKRSHOMAR_PREVIEW'); ?></h3>
		</div>
		<div class="modal-body"></div>
		<div class="modal-footer">
			<button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('COM_ZEKRSHOMAR_CLOSE'); ?></button>
			<button class="btn btn-primary" id="receiveCodeBtn"><?php echo JText::_('COM_ZEKRSHOMAR_RECEIVE_CODE'); ?></button>
		</div>
	</div>
</div>
<div id="code"></div>
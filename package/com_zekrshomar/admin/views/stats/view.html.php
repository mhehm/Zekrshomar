<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_zekrshomar
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * View class for a list of stats.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_zekrshomar
 */
class ZekrshomarViewStats extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * Display the view
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$this->state      = $this->get('State');
		$this->items      = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->users      = $this->get('users');
		$this->zekrs      = $this->get('zekrs');

		ZekrshomarHelper::addSubmenu('stats');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT.'/helpers/zekrshomar.php';

		$state = $this->get('State');
		$canDo = ZekrshomarHelper::getActions($state->get('filter.category_id'));
		// Get the toolbar object instance
		$bar   = JToolBar::getInstance('toolbar');

		JToolbarHelper::title(JText::_('COM_ZEKRSHOMAR_MANAGER_STATS'), 'stats.png');

		if ($canDo->get('core.admin'))
		{
			JToolbarHelper::preferences('com_zekrshomar');
		}

		JHtmlSidebar::setAction('index.php?option=com_zekrshomar&view=stats');

		JHtmlSidebar::addFilter(
			JText::_('COM_ZEKRSHOMAR_SELECT_ZEKR'),
			'filter_zekr_id',
			JHtml::_('select.options', $this->zekrs, 'value', 'text', $this->state->get('filter.zekr_id'))
		);

		JHtmlSidebar::addFilter(
			JText::_('COM_ZEKRSHOMAR_SELECT_USER'),
			'filter_user_id',
			JHtml::_('select.options', $this->users, 'value', 'text', $this->state->get('filter.user_id'))
		);

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_CATEGORY'),
			'filter_category_id',
			JHtml::_('select.options', JHtml::_('category.options', 'com_zekrshomar'), 'value', 'text', $this->state->get('filter.category_id'))
		);
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   3.0
	 */
	protected function getSortFields()
	{
		return array(
			'z.title'    => JText::_('COM_ZEKRSHOMAR_ZEKR_TITLE'),
			'uc.name'    => JText::_('COM_ZEKRSHOMAR_USER_NAME'),
			'a.last'     => JText::_('COM_ZEKRSHOMAR_LAST'),
			'a.number'   => JText::_('COM_ZEKRSHOMAR_NUMBER'),
			'a.id'       => JText::_('JGRID_HEADING_ID')
		);
	}
}

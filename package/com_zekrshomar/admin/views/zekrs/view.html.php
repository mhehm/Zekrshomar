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
 * View class for a list of zekrs.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_zekrshomar
 */
class ZekrshomarViewZekrs extends JViewLegacy
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

		ZekrshomarHelper::addSubmenu('zekrs');

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
		$user  = JFactory::getUser();
		// Get the toolbar object instance
		$bar   = JToolBar::getInstance('toolbar');

		JToolbarHelper::title(JText::_('COM_ZEKRSHOMAR_MANAGER_ZEKRS'), 'zekrs.png');
		if (count($user->getAuthorisedCategories('com_zekrshomar', 'core.create')) > 0)
		{
			JToolbarHelper::addNew('zekr.add');
		}

		if ($canDo->get('core.edit'))
		{
			JToolbarHelper::editList('zekr.edit');
		}

		if ($canDo->get('core.edit.state')) {

			JToolbarHelper::publish('zekrs.publish', 'JTOOLBAR_PUBLISH', true);
			JToolbarHelper::unpublish('zekrs.unpublish', 'JTOOLBAR_UNPUBLISH', true);

			JToolbarHelper::archiveList('zekrs.archive');
			JToolbarHelper::checkin('zekrs.checkin');
		}

		if ($state->get('filter.state') == -2 && $canDo->get('core.delete'))
		{
			JToolbarHelper::deleteList('', 'zekrs.delete', 'JTOOLBAR_EMPTY_TRASH');
		}
		elseif ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::trash('zekrs.trash');
		}

		// Add a batch button
		if ($user->authorise('core.create', 'com_zekrshomar') && $user->authorise('core.edit', 'com_zekrshomar') && $user->authorise('core.edit.state', 'com_zekrshomar'))
		{
			JHtml::_('bootstrap.modal', 'collapseModal');
			$title = JText::_('JTOOLBAR_BATCH');

			// Instantiate a new JLayoutFile instance and render the batch button
			$layout = new JLayoutFile('joomla.toolbar.batch');

			$dhtml = $layout->render(array('title' => $title));
			$bar->appendButton('Custom', $dhtml, 'batch');
		}

		if ($canDo->get('core.admin'))
		{
			JToolbarHelper::preferences('com_zekrshomar');
		}

		JHtmlSidebar::setAction('index.php?option=com_zekrshomar&view=zekrs');

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_state',
			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.state'), true)
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
			'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
			'a.state'    => JText::_('JSTATUS'),
			'a.title'    => JText::_('JGLOBAL_TITLE'),
			'a.created'  => JText::_('JGLOBAL_CREATED'),
			'last'       => JText::_('COM_ZEKRSHOMAR_LAST'),
			'number'     => JText::_('COM_ZEKRSHOMAR_NUMBER'),
			'a.id'       => JText::_('JGRID_HEADING_ID')
		);
	}
}

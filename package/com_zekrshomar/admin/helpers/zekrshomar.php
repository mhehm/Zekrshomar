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
 * Zekrshomar helper.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_zekrshomar
 */
class ZekrshomarHelper
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param   string	The name of the active view.
	 * @since   1.6
	 */
	public static function addSubmenu($vName = 'zekrs')
	{
		JHtmlSidebar::addEntry(
			JText::_('COM_ZEKRSHOMAR_SUBMENU_ZEKRS'),
			'index.php?option=com_zekrshomar&view=zekrs',
			$vName == 'zekrs'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_ZEKRSHOMAR_SUBMENU_CATEGORIES'),
			'index.php?option=com_categories&extension=com_zekrshomar',
			$vName == 'categories'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_ZEKRSHOMAR_SUBMENU_STATS'),
			'index.php?option=com_zekrshomar&view=stats',
			$vName == 'stats'
		);
		if ($vName == 'categories')
		{
			JToolbarHelper::title(
				JText::sprintf('COM_CATEGORIES_CATEGORIES_TITLE', JText::_('com_zekrshomar')),
				'zekrshomar-categories');
		}
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param   integer  The category ID.
	 * @return  JObject
	 * @since   1.6
	 */
	public static function getActions($categoryId = 0)
	{
		$user   = JFactory::getUser();
		$result = new JObject;

		if (empty($categoryId))
		{
			$assetName = 'com_zekrshomar';
			$level     = 'component';
		}
		else
		{
			$assetName = 'com_zekrshomar.category.'.(int) $categoryId;
			$level     = 'category';
		}

		$actions = JAccess::getActions('com_zekrshomar', $level);

		foreach ($actions as $action)
		{
			$result->set($action->name, $user->authorise($action->name, $assetName));
		}

		return $result;
	}
}

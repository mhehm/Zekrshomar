<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_zekrshomar
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

/**
 * Banks Field class for the Joomla Framework.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_zekrshomar
 * @since       1.6
 */
class JFormFieldZekrs extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since   1.6
	 */
	protected $type = 'Zekrs';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 * @since   1.6
	 */
	public function getOptions()
	{
		$options = array();

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('id As value, title As text')
			->from('#__zekrshomar_zekrs AS z')
			->where('z.state = 1')
			->order('z.ordering, z.title');

		// Get the options.
		$db->setQuery($query);

		try
		{
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		return array_merge(parent::getOptions(), $options);
	}
}

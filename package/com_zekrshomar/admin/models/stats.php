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
 * Methods supporting a list of stats records.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_zekrshomar
 */
class ZekrshomarModelStats extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array  An optional associative array of configuration settings.
	 * @see     JController
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'user_id', 'a.user_id', 'user_name',
				'zekr_id', 'a.zekr_id', 'zekr_title',
				'number', 'a.number',
				'last', 'a.last',
				'z.title',
				'uc.name',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$userID = $this->getUserStateFromRequest($this->context . '.filter.user_id', 'filter_user_id');
		$this->setState('filter.user_id', $userID);

		$zekrID = $this->getUserStateFromRequest($this->context . '.filter.zekr_id', 'filter_zekr_id');
		$this->setState('filter.zekr_id', $zekrID);

		$categoryId = $this->getUserStateFromRequest($this->context . '.filter.category_id', 'filter_category_id', '');
		$this->setState('filter.category_id', $categoryId);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_zekrshomar');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.number', 'desc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id    A prefix for the store id.
	 * @return  string  A store id.
	 * @since   1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.user_id');
		$id .= ':' . $this->getState('filter.zekr_id');
		$id .= ':' . $this->getState('filter.category_id');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 * @since   1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		$user  = JFactory::getUser();

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.id, a.user_id, a.zekr_id, a.number, a.last'
			)
		);
		$query->from($db->quoteName('#__zekrshomar_stats') . ' AS a');

		// Join over the users for the sender user.
		$query->select('uc.name AS user_name')
			->join('LEFT', '#__users AS uc ON uc.id = a.user_id');

		// Join over the stats.
		$query->select('z.title AS zekr_title')
			->join('LEFT', '#__zekrshomar_zekrs AS z ON z.id = a.zekr_id');

		// Join over the categories.
		$query->select('c.title AS category_title')
			->join('LEFT', '#__categories AS c ON c.id = z.catid');

		// Filter by user name
		$userId = $this->getState('filter.user_id');
		if (is_numeric($userId))
		{
			$query->where('a.user_id = ' . (int) $userId);
		}

		// Filter by zekr title
		$zekrId = $this->getState('filter.zekr_id');
		if (is_numeric($zekrId))
		{
			$query->where('a.zekr_id = ' . (int) $zekrId);
		}

		// Filter by category.
		$categoryId = $this->getState('filter.category_id');
		if (is_numeric($categoryId))
		{
			$query->where('z.catid = ' . (int) $categoryId);
		}

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->quote('%' . $db->escape($search, true) . '%');
				$query->where('(z.title LIKE ' . $search . ' OR uc.name LIKE ' . $search . ')');
			}
		}

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');
		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}

	/**
	 * Build a list of users
	 *
	 * @return  JDatabaseQuery
	 * @since   1.6
	 */
	public function getUsers()
	{
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Construct the query
		$query->select('u.id AS value, u.name AS text')
			->from('#__users AS u')
			->join('INNER', '#__zekrshomar_stats AS s ON s.user_id = u.id')
			->group('u.id, u.name')
			->order('u.name');

		// Setup the query
		$db->setQuery($query);

		// Return the result
		return $db->loadObjectList();
	}

	/**
	 * Build a list of zekrs
	 *
	 * @return  JDatabaseQuery
	 * @since   1.6
	 */
	public function getZekrs()
	{
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Construct the query
		$query->select('z.id AS value, z.title AS text')
			->from('#__zekrshomar_zekrs AS z')
			->join('INNER', '#__zekrshomar_stats AS s ON s.zekr_id = z.id')
			->group('z.id, z.title')
			->order('z.title');

		// Setup the query
		$db->setQuery($query);

		// Return the result
		return $db->loadObjectList();
	}
}

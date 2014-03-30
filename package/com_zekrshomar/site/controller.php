<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_zekrshomar
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Zekrshomar Component Controller
 *
 * @package     Joomla.Site
 * @subpackage  com_zekrshomar
 */
class ZekrshomarController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param   boolean			If true, the view output will be cached
	 * @param   array  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController		This object to support chaining.
	 * @since   1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$cachable = true;

		// Set the default view name and format from the Request.
		$vName = $this->input->get('view', 'categories');
		$this->input->set('view', $vName);

		$safeurlparams = array('catid' => 'INT', 'id' => 'INT', 'cid' => 'ARRAY', 'year' => 'INT', 'month' => 'INT', 'limit' => 'UINT', 'limitstart' => 'UINT',
			'showall' => 'INT', 'return' => 'BASE64', 'filter' => 'STRING', 'filter_order' => 'CMD', 'filter_order_Dir' => 'CMD', 'filter-search' => 'STRING', 'print' => 'BOOLEAN', 'lang' => 'CMD');

		parent::display($cachable, $safeurlparams);

		return $this;
	}

	// Ajax Zekr
	public static function ajaxZekr()
	{
		$row = array();

		$jinput  = JFactory::getApplication()->input;
		$zekr_id = $jinput->get('zekr_id', '', 'INT');
		$user_id = $jinput->get('user_id', '', 'INT');
		$number  = $jinput->get('number', '', 'INT');

		if($zekr_id && $user_id && $number)
		{
			$db    = JFactory::getDbo();
			$query = 'INSERT INTO #__zekrshomar_stats (user_id,zekr_id,number) VALUES (' . $user_id . ',' . $zekr_id . ',' . $number . ') ON DUPLICATE KEY UPDATE number=number+' . $number . ';';

			$db->setQuery($query);
			$result = $db->execute();

			if($result)
			{
				$query = $db->getQuery(true);

				$query->select('SUM(s.number) AS total, z.number AS usertotal');
				$query->from('#__zekrshomar_stats AS z');
				$query->join('LEFT', '#__zekrshomar_stats s ON (z.zekr_id = s.zekr_id)');
				$query->where('s.zekr_id = ' . $zekr_id . ' AND z.user_id = ' . $user_id);
		
				// Get the options.
				$db->setQuery($query);
		
				try
				{
					$row = $db->loadObject();
				}
				catch (RuntimeException $e)
				{
					JError::raiseWarning(500, $e->getMessage());
				}
			}
		}

		echo json_encode($row);

		$app = JFactory::getApplication();
		$app->close();
	}
}

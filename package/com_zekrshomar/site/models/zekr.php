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
 * @package     Joomla.Site
 * @subpackage  com_zekrshomar
 */
class ZekrshomarModelZekr extends JModelForm
{
	/**
	 * @since   1.6
	 */
	protected $view_item = 'zekr';

	protected $_item = null;

	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $_context = 'com_zekrshomar.zekr';

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since   1.6
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication('site');

		$jform = $app->input->get('jform', array(), 'ARRAY');

		// Load the parameters.
		$params = $app->getParams();

		if(isset($jform['mention_style']))  $params->set('mention_style'   , $jform['mention_style']);
		if(isset($jform['number_style']))   $params->set('number_style'    , $jform['number_style']);
		if(isset($jform['options_style']))  $params->set('options_style'   , $jform['options_style']);
		if(isset($jform['options_values'])) $params->set('options_values'  , $jform['options_values']);
		if(isset($jform['bg_color']))       $params->set('bg_color'        , $jform['bg_color']);
		if(isset($jform['font_color']))     $params->set('font_color'      , $jform['font_color']);

		// Load state from the request.
		$pk = $app->input->getInt('id') ? $app->input->getInt('id') : (isset($jform['id']) ? (int) $jform['id'] : $params->get('zekr_id'));

		$user_id = $app->input->getInt('user_id') ? $app->input->getInt('user_id') : (int) $params->get('user_id');
		$this->setState('zekr.id', $pk);
		$this->setState('zekr.user_id', $user_id);

		$params->set('user_id' , $user_id);

		$this->setState('params', $params);
	}

	/**
	 * Method to get the zekr form.
	 *
	 * The base form is loaded from XML and then an event is fired
	 *
	 *
	 * @param   array  $data		An optional array of data for the form to interrogate.
	 * @param   boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return  JForm	A JForm object on success, false on failure
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_zekrshomar.zekr', 'zekr', array('control' => 'jform', 'load_data' => true));
		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	protected function loadFormData()
	{
		$data = (array) JFactory::getApplication()->getUserState('com_zekrshomar.zekr.data', array());

		return $data;
	}

	/**
	 * Gets a zekr
	 *
	 * @param integer $pk  Id for the zekr
	 *
	 * @return mixed Object or null
	 */
	public function &getItem($pk = null)
	{
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('zekr.id');

		if ($this->_item === null)
		{
			$this->_item = array();
		}

		if (!isset($this->_item[$pk]))
		{
			try
			{
				$db    = $this->getDbo();
				$query = $db->getQuery(true);

				$user    = JFactory::getUser();
				$user_id = $this->getState('zekr.user_id') ? $this->getState('zekr.user_id') : $user->id;

				$query
					->select('z.id, z.title, z.mention, z.image, SUM(s.number) AS sumnumber')
					->from('#__zekrshomar_zekrs AS z')
					->join('LEFT', '#__zekrshomar_stats s ON (z.id = s.zekr_id)')
					->where('z.state = 1 AND z.id = ' . (int) $pk );

				if($user_id)
				{
					$query
						->select('u.number AS usernumber')
						->join('LEFT', '#__zekrshomar_stats u ON (z.id = u.zekr_id AND u.user_id = ' . (int) $user_id . ')');
				}

				$db->setQuery($query);

				$data = $db->loadObject();

				if (empty($data))
				{
					JError::raiseError(404, JText::_('COM_ZEKRSHOMAR_ERROR_ZEKR_NOT_FOUND'));
				}

				$this->_item[$pk] = $data;
			}
			catch (Exception $e)
			{
				$this->setError($e);
				$this->_item[$pk] = false;
			}
		}

		return $this->_item[$pk];
	}
}

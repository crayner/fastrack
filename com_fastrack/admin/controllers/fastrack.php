<?php
/**
 * Fastrack Reader/Viewer is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Fastrack Reader/Viewer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GiCalReader.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package		Fastrack
 * @author		Hill Range Services http://fastrack.hillrange.com.au
 * @copyright	Copyright (C) 2014  Hill Range Services  All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 * @version 14th February 2015
 * @since 9th February 2015
 */

defined('_JEXEC') or die();

class FastrackControllerFastrack extends JControllerBase {
/**
  * Controller Execute
  *
  * @version 14th February 2015
  * @since 9th February 2015
  * @return void
  */
	public function execute() {
	

		$app = $this->getApplication();
		$input = $app->input;

		$taskName = $input->get('task', 'default');
		switch ($taskName) {
			case 'fastrack.cancel':
				$input->set('view', 'default');
				break;
			case 'fastrack.add':
				$input->set('view', 'addfile');
				$input->set('id', 0);
				break;
			case 'fastrack.edit':
				$cid = $input->get('cid', array(), 'ARRAY');
				if (isset($cid[0])) {
					$input->set('view', 'addfile');
					$input->set('id', $cid[0]);
				}
				break;
			case 'fastrack.delete':
				$cid = $input->get('cid', array(), 'ARRAY');
				if (is_array($cid)) 
					foreach ($cid as $id)
						FastrackHelper::deleteFileDefinition($id);				
				break;
			case 'fastrack.edit1':
				$input->set('view', 'addfile');
				break;
			case 'fastrack.apply':
				$id = FastrackHelper::saveFileDefinition();
				$input->set('view', 'addfile');
				break;
			case 'fastrack.save':
				$id = FastrackHelper::saveFileDefinition();
				$input->set('view', 'default');
				break;
			case 'fastrack.save2new':
				$id = FastrackHelper::saveFileDefinition();
				$input->set('id', 0);
				$input->set('view', 'addfile');
				break;
			case 'fastrack.save2copy':
				$input->set('id', 0);
				$input->set('name', 'copy_of_'.$input->getString('name'));
				$id = FastrackHelper::saveFileDefinition();
				$input->set('view', 'addfile');
				break;
			case 'fastrack.test':
				$input->set('view', 'test');				
				break;
			case 'default':
				break;
			default:
				printAnObject($taskName);
		}
		// Get the document object.
		$document = $app->getDocument();
		$viewName     = $input->get('view', 'default');
		$viewFormat   = $document->getType();
		// Register the layout paths for the view
		$paths = new SplPriorityQueue;
		$paths->insert(JPATH_COMPONENT . '/views/' . $viewName . '/tmpl', 'normal');
		$viewClass  = 'FastrackViews' . ucfirst($viewName) . ucfirst($viewFormat);
		$modelClass  = 'FastrackModels' . ucfirst($viewName) ;
		$layoutName = $input->get('layout', 'default');

		$view = new $viewClass(new $modelClass, $paths);
		$view->setLayout($layoutName);

		if ($viewName !== 'addfile') {
			JSubMenuHelper::addEntry(JText::_('COM_FASTRACK_CPANEL'), 'index.php?option=com_fastrack&view=default', $viewName == 'default');
			JSubMenuHelper::addEntry(JText::_('COM_FASTRACK_LISTFILES'), 'index.php?option=com_fastrack&view=listfiles', $viewName == 'listfiles');
			JSubMenuHelper::addEntry(JText::_('COM_FASTRACK_ADDFILE'), 'index.php?option=com_fastrack&view=addfile', $viewName == 'addfile');
		}
    	// Render our view.
		echo $view->render();
	 
		return true;

	}
/**
  * Controller Construct
  *
  * @version 9th February 2015
  * @since 9th February 2015
  * @return void
  */
	public function __construct() {
	
		parent::__construct();
		return ;
	}
}
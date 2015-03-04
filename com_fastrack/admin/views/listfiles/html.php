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
 * along with Fastrack.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package		Fastrack
 * @author		Hill Range Services http://fastrack.hillrange.com.au
 * @copyright	Copyright (C) 2014  Hill Range Services  All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 * @version 13th February 2015
 * @since 13th February 2015
 */

defined('_JEXEC') or die();
/**
 * Fastrack View List Files HTML Class
 *
 * @version 13th February 2015
 * @since 13th February 2015
 */
class FastrackViewsListfilesHtml extends JViewHtml{

	protected $icon = 'fastrack';
	protected $title = 'COM_FASTRACK_TITLE';
	protected $items = NULL;
	protected $pagination = NULL;
	protected $limitstart = 0;
	protected $limit = 10;
	protected $total = NULL;
	protected $model = NULL;

/**
 * Construct
 * 
 * @version 13th February 2015
 * @since 13th February 2015
 * @return 
 */
 	public function __construct($modelClass, $paths) {
	
		return parent::__construct($modelClass, $paths);
	}
/**
 * Add Toolbar
 *
 * @version 13th February 2015
 * @since 13th February 2015
 * return void
 */
	protected function addToolbar() {

		$canDo = FastrackHelper::getActions();
		if ($canDo->get('core.create')) {
			JToolBarHelper::addNew('fastrack.add', 'JTOOLBAR_NEW');
		}
		if ($canDo->get('core.edit')) {
			JToolBarHelper::editList('fastrack.edit', 'JTOOLBAR_EDIT');
		}
		if ($canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'fastrack.delete', 'JTOOLBAR_DELETE');
		}
		JToolBarHelper::cancel('fastrack.cancel', 'JTOOLBAR_CLOSE');
		return ;
	}
/**
 * Render
 *
 * @version 13th February 2015
 * @since 13th February 2015
 * return void
 */
	public function render() {
		
		JToolBarHelper::title(JText::_('COM_FASTRACK'), 'fastrack');
		if ($this->title == 'COM_FASTRACK_TITLE') 
			$this->title = JText::_('COM_FASTRACK_TITLE');
		$input = JFactory::getApplication()->input;
		$this->limitstart = $input->get('limitstart', $this->limitstart);
		$this->limit = $input->get('limit', $this->limit);
		$this->items = $this->model->execute($this->limitstart, $this->limit);
		$this->total =  $this->model->getFileTotal();
		$this->pagination = new JPagination( $this->total, $this->limitstart, $this->limit, NULL);
		$this->addToolBar();
 		return parent::render();
	}
	
}
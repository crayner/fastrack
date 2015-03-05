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
 * @version 11th February 2015
 * @since 11th February 2015
 */

defined('_JEXEC') or die();
/**
 * Fastrack Views Addfile Html Class
 * 
 * @version 11th February 2015
 * @since 11th February 2015
 */
class FastrackViewsAddfileHtml extends JViewHtml{
/**
 * Construct
 * 
 * @version 11th February 2015
 * @since 11th February 2015
 * @return 
 */
 	public function __construct($modelClass, $paths) {
	
		return parent::__construct($modelClass, $paths);
	}
/**
 * Render
 * 
 * @version 11th February 2015
 * @since 11th February 2015
 * @return string
 */
 	public function render() {
		
		JToolBarHelper::title(JText::_('COM_FASTRACK_ADDFILE'), 'Fastrack Title');
		$this->form = JForm::getInstance('adminForm', JPATH_ADMINISTRATOR.'/components/com_fastrack/models/forms/fastrack.xml');
		$this->addToolbar();
		foreach ((array)$this->model->ftfile as $q=>$w)
			$this->form->setValue($q, NULL, $w);
		return parent::render();
	}
/**
 * add ToolBar
 * 
 * @version 11th February 2015
 * @since 11th February 2015
 * @return void
 */
	protected function addToolbar() {
		
		$input = JFactory::getApplication()->input;
		$input->set('hidemainmenu', true);
		$canDo = FastrackHelper::getActions($this->model->ftfile->id);
		if ($this->model->ftfile->id < 1) {
			if ($canDo->get('core.create')) {
				JToolBarHelper::apply('fastrack.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('fastrack.save', 'JTOOLBAR_SAVE');
				JToolBarHelper::custom('fastrack.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
			}
			JToolBarHelper::cancel('fastrack.cancel', 'JTOOLBAR_CANCEL');
		} else {
			if ($canDo->get('core.edit')) {
				JToolBarHelper::apply('fastrack.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('fastrack.save', 'JTOOLBAR_SAVE');

				if ($canDo->get('core.create')) {
					JToolBarHelper::custom('fastrack.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
				}
			}
			if ($canDo->get('core.create')) {
				JToolBarHelper::custom('fastrack.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
			}
			JToolBarHelper::cancel('fastrack.cancel', 'JTOOLBAR_CLOSE');
		}
		return ;
	}
}
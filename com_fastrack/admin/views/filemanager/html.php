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
 * @version 9th February 2015
 * @since 9th February 2015
 */

defined('_JEXEC') or die();

JLoader::import('components.com_fastrack.libraries.view', JPATH_ADMINISTRATOR);

/**
  * Fastrack View Default HTML Class
  *
  * @version 9th February 2015
  * @since 9th February 2015
  */
class FastrackViewsFilemanagerHtml extends FastrackViews {
/**
 * Fastrack Object
 * @var object
 */
 	protected $fastrack = NULL;
/**
 * Form
 * @var object
 */
 	protected $form = NULL;
/**
  * Render
  *
  * @version 9th February 2015
  * @since 9th February 2015
  * @param string TPL
  * @return string
  */
  	public function render($tpl = NULL){
		
		$input = JFactory::getApplication()->input;
		
		$this->fastrack = unserialize ( base64_decode ( $input->get ( 'Item' ) ) ) ;
		if (! isset ( $this->fastrack->id ) ) {
			$this->fastrack = new stdClass();
			$this->fastrack->id = 0;
			$this->fastrack->name = NULL;

            $this->access = NULL;
            $this->access_content = NULL;
		}
		$this->form = JForm::getInstance('adminForm', JPATH_ADMINISTRATOR.'/components/com_fastrack/models/forms/fastrack.xml');
		$f = (array) $this->fastrack;
		foreach ( $f as $q=>$w ) 
			$this->form->setValue($q, NULL, $w);
		$this->addToolbar();
		return parent::render($tpl);
	}
/**
  * Add Toolbar
  *
  * @version 9th February 2015
  * @since 9th February 2015
  * @param string TPL
  * @return string
  */
	protected function addToolbar() {
		
		$input = JFactory::getApplication()->input;
		$input->set('hidemainmenu', true);

		$canDo = FastrackHelper::getActions($this->fastrack->id);
		if ($this->fastrack->id < 1) {
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
	}
}
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
 * @version 13th February 2015
 * @since 13th February 2015
 */

defined('_JEXEC') or die();

?>
<h3><?php echo JText::_('COM_FASTRACK_TEST_TITLE'); ?></h3>
<?php
if (! empty($this->ftfile)): 
	foreach ($this->results as $q=>$w) : ?>
    	<p><?php echo JTEXT::_('COM_FASTRACK_FIELD_'.strtoupper($q).'_LABEL').': '.$this->ftfile->$q.': '.$w; ?></p>
	<?php endforeach ?>
<?php endif ?>

<div align="center" style="clear: both">
	<br>
    <?php $params = JFactory::getApplication()->input;
	echo JText::_('COM_FASTRACK_FOOTER').'. Version: '.$params->get('FASTRACK_VERSION');?>
</div>

<?php

printAnObject($this);
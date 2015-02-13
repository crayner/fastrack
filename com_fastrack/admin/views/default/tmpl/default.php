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
 * @version 12th February 2015
 * @since 11th Fenruary 2015
 */

defined('_JEXEC') or die();

JFactory::getDocument()->addStyleSheet('components/com_fastrack/views/default/tmpl/default.css');
?>
<h2><?php echo JText::_('COM_FASTRACK_CPANEL_WELCOME'); ?></h2>
<p><?php echo JText::_('COM_FASTRACK_CPANEL_INTRO'); ?></p>

<!-- cpanel View -->
<div id="cpanel" style="float:left">
    <div style="float:left; margin-right: 20px">
        <div class="icon">
            <a href="index.php?option=com_fastrack&view=listfiles" >
            <img src="<?php echo JURI::base(true);?>/../media/com_fastrack/images/FileListIcon.png" height="50px" width="50px">
            <span><?php echo JText::_('COM_FASTRACK_LISTFILES'); ?></span>
            </a>
        </div>
        <div class="icon">
            <a href="index.php?option=com_fastrack&view=addfile" >
            <img src="<?php echo JURI::base(true);?>/../media/com_fastrack/images/FileAddIcon.png" height="50px" width="50px">
            <span><?php echo JText::_('COM_FASTRACK_ADDFILE'); ?></span>
            </a>
        </div>
    </div>
</div>

<div align="center" style="clear: both">
	<br>
    <?php $params = JFactory::getApplication()->input;
	echo JText::_('COM_FASTRACK_FOOTER').'. Version: '.$params->get('FASTRACK_VERSION');?>
</div>
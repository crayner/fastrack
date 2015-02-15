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

JHtml::_('behavior.tooltip');
?>

<form action="<?php echo JRoute::_('index.php?option=com_fastrack&view=listfiles'); ?>" method="post" name="adminForm" id="adminForm">
<table class="table table-striped" id="eventList">
	<thead>
		<tr>
			<th width="1%" class="hidden-phone">
				<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
			</th>
			<th class="title" width="20%">
				<?php echo JText::_('COM_FASTRACK_FIELD_NAME_LABEL'); ?>
			</th>
			<th>
				<?php echo JText::_('COM_FASTRACK_FIELD_PATH_LABEL'); ?>
			</th>
			<th>
				<?php echo JText::_('COM_FASTRACK_FIELD_RESULTPATH_LABEL'); ?>
			</th>
			<th>
				<?php echo JText::_('COM_FASTRACK_FIELD_IMAGEURL_LABEL'); ?>
			</th>
			<th>
				<?php echo JText::_('COM_FASTRACK_TESTFILE'); ?>
			</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($this->items as $i => $item) {?>
		<tr class="row<?php echo $i % 2; ?>">
				<td class="center hidden-phone">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				<td class="nowrap has-context">
					<a href="<?php echo JRoute::_( 'index.php?option=com_fastrack&task=fastrack.edit1&id='. $item->id ); ?>" title="<?php echo JText::_('JACTION_EDIT');?>">
						<?php echo $this->escape($item->name); ?>
					</a>
				</td>
				<td class="nowrap has-context"><?php echo $item->path ?></td>
				<td class="nowrap has-context"><?php echo $item->resultPath; ?></td>
				<td class="nowrap has-context"><?php echo $item->imageURL; ?></td>
                <td class="nowrap has-context"><a href="<?php echo JRoute::_( 'index.php?option=com_fastrack&task=fastrack.test&id='. $item->id ); ?>" title="<?php echo JText::_('JACTION_TEST');?>">
						Test &amp; Parse
					</a></td>
		</tr>
		<?php } ?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="6">
				<?php echo $this->pagination->getListFooter(); ?>
				<br/><br/>
			</td>
		</tr>
	</tfoot>
</table>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHtml::_('form.token'); ?>
</form>

<div align="center" style="clear: both">
	<br>
    <?php $params = JFactory::getApplication()->input;
	echo JText::_('COM_FASTRACK_FOOTER').'. Version: '.$params->get('FASTRACK_VERSION');?>
</div>
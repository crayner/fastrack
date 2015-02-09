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


FastrackHelper::loadLibrary();

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');

$fastrack = $this->fastrack;

$input = JFactory::getApplication()->input;

?>
<script type="text/javascript" language="jscript">


	Joomla.submitbutton = function(task) {
		if (task == 'fastrack.cancel' || document.formvalidator.isValid(document.id('fastrack-form'))) {
			Joomla.submitform(task, document.getElementById('fastrack-form'));
		} else {
			alert('<?php echo $this->escape(JText::_("JGLOBAL_VALIDATION_FORM_FAILED")); ?>');
		}
	}
	
	
</script>

<form action="<?php echo JRoute::_('index.php?option=com_fastrack&view=filemanager&layout=edit&id='.(int) @$fastrack->id); ?>" method="post" name="adminForm" id="fastrack-form" class="form-validate">
    <div class="row-fluid">
        <!-- Begin Content -->
        <div class="span10 form-horizontal">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#general" data-toggle="tab"><?php echo empty($this->item->id) ? JText::_('COM_FASTRACK_VIEW_DETAILS') : JText::sprintf('COM_FASTRACK_VIEW_DETAILS', $this->item->id); ?></a></li>
            </ul>
            <div class="tab-content">
                <!-- Begin Tabs -->
                <div class="tab-pane active" id="general">
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="control-group">
                                <div class="control-label">
                                    <?php echo $this->form->getLabel('name'); ?>
                                </div>
                                <div class="controls">
                                    <?php echo $this->form->getInput('name'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>


<h2><?php echo JText::_('COM_FASTRACK_WELCOME'); ?></h2>
<h3>File Addition</h3>
<div align="center" style="clear: both">
	<br>
<?php
$input = JFactory::getApplication()->input;
echo sprintf(JText::_('COM_FASTRACK_FOOTER'), $input->get('FASTRACK_VERSION'));?>
</div>

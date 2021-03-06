<?php
/**
 * Items Model for a Workflow Component.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_workflow
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       4.0.0
 */
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

HTMLHelper::_('behavior.multiselect');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

$saveOrder = $listOrder == 'w.ordering';

$orderingColumn = 'created';
$saveOrderingUrl = '';

if (strpos($listOrder, 'modified') !== false)
{
	$orderingColumn = 'modified';
}

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_workflow&task=workflows.saveOrderAjax&tmpl=component&extension=' . $this->escape($this->extension) . '&' . Session::getFormToken() . '=1';
	HTMLHelper::_('draggablelist.draggable');
}

$extension = $this->escape($this->state->get('filter.extension'));

$user = Factory::getUser();
$userId = $user->id;
?>
<form action="<?php echo Route::_('index.php?option=com_workflow&view=workflows&extension=' . $extension); ?>" method="post" name="adminForm" id="adminForm">
	<div class="row">
		<?php if (!empty($this->sidebar)) : ?>
			<div id="j-sidebar-container" class="col-md-2">
				<?php echo $this->sidebar; ?>
			</div>
		<?php endif; ?>
		<div class="<?php if (!empty($this->sidebar)) {echo 'col-md-10'; } else { echo 'col-md-12'; } ?>">
			<div id="j-main-container" class="j-main-container">
				<?php
					// Search tools bar
					echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));
				?>
				<?php if (empty($this->workflows)) : ?>
					<div class="j-alert j-alert-info d-flex mt-4">
						<div class="j-alert-icon-wrap"><span class="icon-info-circle" aria-hidden="true"></span><span class="sr-only"><?php echo Text::_('INFO'); ?></span></div>
						<div class="j-alert-info-wrap"><?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?></div>
					</div>
				<?php else: ?>
					<table class="table j-list-table">
						<caption id="captionTable" class="sr-only">
							<?php echo Text::_('COM_WORKFLOW_WORKFLOWS_TABLE_CAPTION'); ?>, <?php echo Text::_('JGLOBAL_SORTED_BY'); ?>
						</caption>
						<thead>
							<tr>
								<td scope="col" class="text-center" style="width: 3rem;">
									<?php echo HTMLHelper::_('grid.checkall'); ?>
								</td>
								<th scope="col" class="text-center d-none d-md-table-cell" style="width: 3rem;">
									<?php echo HTMLHelper::_('searchtools.sort', '', 'w.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-caret-v'); ?>
								</th>
								<th scope="col"  class="text-center d-none d-md-table-cell" style="width: 3rem;">
									<?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'w.published', $listDirn, $listOrder); ?>
								</th>
								<th scope="col">
									<?php echo HTMLHelper::_('searchtools.sort', 'COM_WORKFLOW_NAME', 'w.title', $listDirn, $listOrder); ?>
								</th>
								<th scope="col" class="text-center" style="width: 3rem;">
									<?php echo Text::_('COM_WORKFLOW_DEFAULT'); ?>
								</th>
								<th scope="col" class="d-none d-md-table-cell" style="width: 3rem;">
									<?php echo Text::_('COM_WORKFLOW_COUNT_STAGES'); ?>
								</th>
								<th scope="col" class="d-none d-md-table-cell" style="width: 3rem;">
									<?php echo Text::_('COM_WORKFLOW_COUNT_TRANSITIONS'); ?>
								</th>
								<th scope="col" class="d-none d-md-table-cell" style="width: 3rem;">
									<?php echo HTMLHelper::_('searchtools.sort', 'COM_WORKFLOW_ID', 'w.id', $listDirn, $listOrder); ?>
								</th>
							</tr>
						</thead>
						<tbody <?php if ($saveOrder) :?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($listDirn); ?>" data-nested="false"<?php endif; ?>>
						<?php foreach ($this->workflows as $i => $item):
							$states = Route::_('index.php?option=com_workflow&view=stages&workflow_id=' . $item->id . '&extension=' . $extension);
							$transitions = Route::_('index.php?option=com_workflow&view=transitions&workflow_id=' . $item->id . '&extension=' . $extension);
							$edit = Route::_('index.php?option=com_workflow&task=workflow.edit&id=' . $item->id . '&extension=' . $extension);

							$isCore     = !empty($item->core);
							$canEdit    = $user->authorise('core.edit', $extension . '.workflow.' . $item->id);
							// @TODO set proper checkin fields
							$canCheckin = true || $user->authorise('core.admin', 'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
							$canEditOwn = $user->authorise('core.edit.own',   $extension . '.workflow.' . $item->id) && $item->created_by == $userId;
							$canChange  = $user->authorise('core.edit.state', $extension . '.workflow.' . $item->id) && $canCheckin;
							?>
							<tr class="row<?php echo $i % 2; ?>" data-dragable-group="0">
								<td class="order text-center">
									<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
								</td>
								<td class="order text-center d-none d-md-table-cell">
									<?php
									$iconClass = '';
									if (!$canChange)
									{
										$iconClass = ' inactive';
									}
									elseif (!$saveOrder)
									{
										$iconClass = ' inactive" title="' . Text::_('JORDERINGDISABLED');
									}
									?>
									<span class="sortable-handler icon-move-v<?php echo $iconClass ?>">
										<span class="icon-arrows-v" aria-hidden="true"></span>
									</span>
									<?php if ($canChange && $saveOrder) : ?>
										<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order">
									<?php endif; ?>
								</td>
								<td class="text-center d-none d-md-table-cell">
									<?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'workflows.', $canChange && !$isCore); ?>
								</td>
								<th scope="row">
									<?php if (!$isCore && ($canEdit || $canEditOwn)) : ?>
										<a href="<?php echo $edit; ?>" title="<?php echo Text::_('JACTION_EDIT', true); ?> <?php echo Text::_($item->title, true); ?>">
											<?php echo $this->escape(Text::_($item->title)); ?>
										</a>
										<div class="small"><?php echo $item->description; ?></div>
									<?php else: ?>
										<?php echo $this->escape(Text::_($item->title)); ?>
										<div class="small"><?php echo $item->description; ?></div>
									<?php endif; ?>
								</th>
								<td class="text-center">
									<?php echo HTMLHelper::_('jgrid.isdefault', $item->default, $i, 'workflows.', $canChange); ?>
								</td>
								<td class="d-none d-md-table-cell">
									<a title="<?php echo Text::_('COM_WORKFLOW_COUNT_STAGES', true); ?>" href="<?php echo Route::_('index.php?option=com_workflow&view=stages&workflow_id=' . (int) $item->id . '&extension=' . $extension); ?>">
										<u><?php echo $item->count_states; ?></u></a>
								</td>
								<td class="d-none d-md-table-cell">
									<a title="<?php echo Text::_('COM_WORKFLOW_COUNT_TRANSITIONS', true); ?>" href="<?php echo Route::_('index.php?option=com_workflow&view=transitions&workflow_id=' . (int) $item->id . '&extension=' . $extension); ?>">
										<u><?php echo $item->count_transitions; ?></u></a>
								</td>
								<td class="d-none d-md-table-cell">
									<?php echo $item->id; ?>
								</td>
							</tr>
						<?php endforeach ?>
					</table>
					
					<!-- load the pagination. -->
					<div class="j-pagination-footer">
						<?php echo LayoutHelper::render('joomla.searchtools.default.listlimit', array('view' => $this)); ?>
						<?php echo $this->pagination->getListFooter(); ?>
					</div>
				<?php endif; ?>
				<input type="hidden" name="task" value="">
				<input type="hidden" name="boxchecked" value="0">
				<input type="hidden" name="extension" value="<?php echo $extension ?>">
				<?php echo HTMLHelper::_('form.token'); ?>
			</div>
		</div>
	</div>
</form>

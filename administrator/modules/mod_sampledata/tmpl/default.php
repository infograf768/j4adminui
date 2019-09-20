<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  mod_sampledata
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('bootstrap.framework');
HTMLHelper::_('form.csrf');
HTMLHelper::_('script', 'mod_sampledata/sampledata-process.js', ['version' => 'auto', 'relative' => true]);

Text::script('MOD_SAMPLEDATA_CONFIRM_START');
Text::script('MOD_SAMPLEDATA_ITEM_ALREADY_PROCESSED');
Text::script('MOD_SAMPLEDATA_INVALID_RESPONSE');

$app->getDocument()->addScriptOptions(
	'sample-data',
	[
		'icon' => Uri::root(true) . '/media/system/images/ajax-loader.gif'
	]
);
?>
<?php if ($items) : ?>
	<ul id="sample-data-wrapper" class="list-group list-group-flush">
		<?php foreach($items as $i => $item) : ?>
			<li class="list-group-item sampledata-<?php echo $item->name; ?>">
				<div class="d-flex justify-content-between align-items-center">
					<div class="mr-2">
						<span class="fas fa-<?php echo $item->icon; ?> mr-1" aria-hidden="true"></span>
						<?php echo htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8'); ?>
					</div>
					<button type="button" class="btn btn-link btn-sm apply-sample-data" data-type="<?php echo $item->name; ?>" data-steps="<?php echo $item->steps; ?>">
						<span class="fa fa-upload" aria-hidden="true"></span> <?php echo Text::_('JLIB_INSTALLER_INSTALL'); ?>
						<span class="sr-only"><?php echo $item->title; ?></span>
					</button>
				</div>
				<p class="mt-1 text-muted"><?php echo $item->description; ?></p>
			</li>
			<?php // Progress bar ?>
			<li class="list-group-item sampledata-progress-<?php echo $item->name; ?> d-none">
				<div class="progress">
					<div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"></div>
				</div>
			</li>
			<?php // Progress messages ?>
			<li class="list-group-item sampledata-progress-<?php echo $item->name; ?> d-none">
				<ul class="list-unstyled"></ul>
			</li>
		<?php endforeach; ?>
	</ul>
<?php else : ?>
	<div class="alert alert-warning">
		<span class="fa fa-exclamation-circle" aria-hidden="true"></span><span class="sr-only"><?php echo Text::_('WARNING'); ?></span>
		<?php echo Text::_('MOD_SAMPLEDATA_NOTAVAILABLE'); ?>
	</div>
<?php endif; ?>

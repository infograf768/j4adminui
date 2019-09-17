<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('JPATH_BASE') or die;
$data = $displayData;
?>
<div class="js-stools-field-selector">
	<div class="sr-only">
		<?php echo $data['view']->filterForm->getField($data['options']['selectorFieldName'])->label; ?>
	</div>
	<?php echo $data['view']->filterForm->getField($data['options']['selectorFieldName'])->input; ?>
</div>
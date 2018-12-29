<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  mod_feed
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Language\Text;

// Check if feed URL has been set
if (empty ($rssurl))
{
	echo '<div>' . Text::_('MOD_FEED_ERR_NO_URL') . '</div>';

	return;
}

if (!empty($feed) && is_string($feed))
{
	echo $feed;
}
else
{
	$lang      = Factory::getLanguage();
	$myrtl     = $params->get('rssrtl');
	$direction = ' ';

	if ($lang->isRtl() && $myrtl == 0)
	{
		$direction = ' redirect-rtl';
	}
	// Feed description
	elseif ($lang->isRtl() && $myrtl == 1)
	{
		$direction = ' redirect-ltr';
	}
	elseif ($lang->isRtl() && $myrtl == 2)
	{
		$direction = ' redirect-rtl';
	}
	elseif ($myrtl == 0)
	{
		$direction = ' redirect-ltr';
	}
	elseif ($myrtl == 1)
	{
		$direction = ' redirect-ltr';
	}
	elseif ($myrtl == 2)
	{
		$direction = ' redirect-rtl';
	}

	if ($feed != false) :
		// Image handling
		$iUrl   = $feed->image ?? null;
		$iTitle = $feed->imagetitle ?? null;
		?>
		<div style="direction: <?php echo $rssrtl ? 'rtl' :'ltr'; ?>; text-align: <?php echo $rssrtl ? 'right' :'left'; ?> !important" class="feed<?php echo $moduleclass_sfx; ?>">
		<?php

		// Feed description
		if (!is_null($feed->title) && $params->get('rsstitle', 1)) : ?>
			<h2 class="<?php echo $direction; ?>">
				<a href="<?php echo str_replace('&', '&amp;', $rssurl); ?>" target="_blank">
				<?php echo $feed->title; ?></a>
			</h2>
		<?php endif; ?>

		<?php // Feed description ?>
		<?php if ($params->get('rssdesc', 1)) : ?>
			<?php echo $feed->description; ?>
		<?php endif; ?>

		<?php // Feed image ?>
		<?php if ($params->get('rssimage', 1) && $iUrl) : ?>
			<img src="<?php echo $iUrl; ?>" alt="<?php echo @$iTitle; ?>">
		<?php endif; ?>


	<?php // Show items ?>
	<?php if (!empty($feed)) : ?>
		<ul class="newsfeed<?php echo $params->get('moduleclass_sfx'); ?> list-group">
		<?php for ($i = 0; $i < $params->get('rssitems', 5); $i++) :

			if (!$feed->offsetExists($i)) :
				break;
			endif;
			$uri  = $feed[$i]->uri || !$feed[$i]->isPermaLink ? trim($feed[$i]->uri) : trim($feed[$i]->guid);
			$uri  = !$uri || stripos($uri, 'http') !== 0 ? $params->get('rsslink') : $uri;
			$text = $feed[$i]->content !== '' ? trim($feed[$i]->content) : '';
			?>
				<li class="list-group-item mb-2">
					<?php if (!empty($uri)) : ?>
						<h5 class="feed-link">
						<a href="<?php echo $uri; ?>" target="_blank">
						<?php echo trim($feed[$i]->title); ?></a></h5>
					<?php else : ?>
						<h5 class="feed-link"><?php echo trim($feed[$i]->title); ?></h5>
					<?php endif; ?>

					<?php if ($params->get('rssitemdesc') && $text !== '') : ?>
						<div class="feed-item-description">
						<?php
							// Strip the images.
							$text = OutputFilter::stripImages($text);
							// Strip HTML
							$text = strip_tags($text);
							echo str_replace('&apos;', "'", $text);
						?>
						</div>
					<?php endif; ?>
				</li>
		<?php endfor; ?>
		</ul>
	<?php endif; ?>
	</div>
	<?php endif;
}

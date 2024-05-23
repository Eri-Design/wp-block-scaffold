<?php
/**
 * Social Sharing Icons
 * @package EriScaffoldTheme
 */

return array(
	'title'      => __( 'Social Sharing', 'eri-scaffold-block-theme' ),
	'categories' => array( 'eri-scaffold' ),
	'inserter'   => true,
	'content'    => '<!-- wp:group {"align":"wide","layout":{"type":"constrained"},"metadata":{"name":"Social Sharing"}} -->
<div class="wp-block-group alignwide"><!-- wp:group {"style":{"spacing":{"blockGap":"0"}},"className":"eri-scaffold__social-share","layout":{"type":"default"}} -->
<div class="wp-block-group eri-scaffold__social-share"><!-- wp:paragraph {"style":{"typography":{"fontWeight":"700"}},"fontSize":"normal","fontFamily":"merriweather-sans"} -->
<p class="has-merriweather-sans-font-family has-normal-font-size" style="font-weight:700">SHARE</p>
<!-- /wp:paragraph -->

<!-- wp:outermost/social-sharing {"iconBackgroundColor":"primary","iconBackgroundColorValue":"#A41D36","size":"has-normal-icon-size","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|20"}}},"className":"is-style-default","layout":{"type":"flex","orientation":"horizontal"}} -->
<ul class="wp-block-outermost-social-sharing has-normal-icon-size has-icon-background-color is-style-default"><!-- wp:outermost/social-sharing-link {"service":"facebook","label":"Share on Facebook"} /-->

<!-- wp:outermost/social-sharing-link {"service":"x","label":"Share on X"} /-->

<!-- wp:outermost/social-sharing-link {"service":"linkedin","label":"Share on Linkedin"} /--></ul>
<!-- /wp:outermost/social-sharing --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->',
);
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>

	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	

	<?php if(function_exists('wp_head')) { wp_head(); } ?>

	<link href="<?php echo ADNI_ASSETS_URL; ?>/dist/_ning_admin.bundle.js.css" rel="stylesheet" type="text/css" media="screen" />
	<link href="<?php echo ADNI_ASSETS_URL; ?>/dist/_ning_frontend_manager.bundle.js.css" rel="stylesheet" type="text/css" media="screen" />
	<link href="https://fonts.googleapis.com/css?family=Hind:300,700" rel="stylesheet">
</head>
<body <?php body_class(); ?>>



<header>
</header>

<nav class="top_bar"> <!-- css_sticky -->
	<div class="inner_content clear">
		<a href="">
			<span class="logo">
				<svg class="_dn_ani" viewBox="0 0 562 577.471" style="opacity: 1;">
				<g>
					<path class="colorful c1" fill="none" stroke="#000000" stroke-width="6.3853" stroke-linecap="round" d="
						M426.056,110.615c-4.884-17.071-12.84-32.996-18.556-49.722l7-4.857c27.306,14.704,52.93,36.27,71.752,54.109
						c74.347,70.466,82.461,149.52,61.6,240.495c-0.918,4.004-2.994,11.451-4.861,18.039c-22.888,80.746-68.216,147.818-144.187,178.374
						c-18.54,7.457-41.594,14.001-67.079,18.355C182.835,590.849,30.643,542.229,9.321,374.183c-2.956-23.294-5.007-58.902-2.305-88.062
						C21.267,132.373,143.822-22.6,313.363,14.711" style="stroke-dasharray: 1717.16, 1717.16; stroke-dashoffset: 0;"></path>
					<path class="colorful c2" fill="none" stroke="#000000" stroke-width="5.5888" stroke-linecap="round" d="
						M214.5,471.893c17.435-47.895,33.938-95.657,46.333-145.104c-16.147-1.152-32.94-1.004-49.236-1.965
						c13.073-56.969,23.37-120.021,49.154-173.074c38.938-0.785,80.387-5.217,118.124,1.99c-29.695,37.611-61.574,76.14-81.897,119.612
						c28.546,3.878,59.042-5.488,86.875-1.204c-56.624,57.441-88.036,107.949-128.104,158.994
						c-12.692,16.169-25.556,35.718-38.998,47.001L214.5,471.893z" style="stroke-dasharray: 1005.03, 1005.03; stroke-dashoffset: 0;"></path>
				</g>
				</svg>
			</span>
			<span class="title">Adning</span>
			<span class="side-title">
				<?php _e('Frontend AD Manager','adn'); ?>
			</span>
		</a>

		<?php
		/*<a href="#" class="_imgMCE_btn button" style="margin-top: 10px;">
			<div class="logo_holder"><svg x="0px" y="0px" width="19px" height="15px" viewBox="0 0 310 426">
			<g><g><g><g><path fill="#FFFF00" d="M237,225c-0.33,0-0.67,0-1,0c-53.73,59.93-108.85,118.49-163,178c-0.85,0.18-0.94-0.39-1-1
			c26.08-58.92,51.7-118.3,77-178C178,224.67,209.67,222.67,237,225z"></path></g><g></g></g></g><g><g><g><path fill="#FFFF00" d="M289,165c0,1.33,0,2.67,0,4c-17.86,18.48-34.64,38.03-52,57c-56,0-112,0-168,0c0-1,0-2,0-3
			c21.5-64.83,42.62-130.05,63-196c46.33,0,92.67,0,139,0c-27.25,45.75-54.78,91.22-81,138C223,165,256,165,289,165z"></path></g><g></g></g></g></g><g><g id="bottom_xA0_Image_1_"><g><g><path fill="#D7CB05" d="M149,225c0,1.33,0,2.67,0,4c-26.73,57.27-50.59,117.41-77,175c-16.02-1.31-37.3,2.63-50-2
			c25.93-59.4,52.98-117.69,79-177C117,225,133,225,149,225z"></path></g><g></g></g></g><g><g><g><path fill="#D7CB05" d="M133,27c0,1,0,2,0,3c-21.17,64.5-41.92,129.41-62,195c-16.33,0-32.67,0-49,0c0-1,0-2,0-3
			C43.17,157.5,63.92,92.59,84,27C100.33,27,116.67,27,133,27z"></path></g><g></g></g></g></g></svg></div>
			<div class="text_holder">GET IT NOW</div>
		</a>*/
		?>
	</div>
</nav>
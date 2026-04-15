<?php
/**
 * Template Name: Forms
 *
 * This template prints a hard coded form for preview.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Acme Media
 */

get_header(); ?>

	<div class="wrap">
		<div class="primary content-area">
			<main id="main" class="site-main" role="main">
				<div style="background: #000; padding: 10px;">
					<form>
						<div>
							<label for="text-input">Text Input</label>
							<input type="text" id="text-input" name="text-input">
						</div>

						<div>
							<?php
								// Set arguments for my fancy dropdown.
								$args = array(
									'label' => 'Dropdown',
									'name' => 'dropdown',
									'options' => array(
										'Option 1' => 'option1',
										'Option 2' => 'option2',
										'Option 3' => 'option3',
										'Option 4' => 'option4',
									),
								);

								wds_acme_do_fancy_selectbox( $args );
							?>
						</div>

						<div>
							<label for="textarea-input">Textarea</label>
							<textarea id"textarea-input" name="textarea-input"></textarea>
						</div>
					</form>
				</div>

			</main><!-- #main -->
		</div><!-- .primary -->

		<?php get_sidebar(); ?>

	</div><!-- .wrap -->

<?php get_footer(); ?>

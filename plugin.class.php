<?php
/**
* Plugin Main Class
*/
class WCP_Filterable_Store
{
	
	function __construct()
	{
		add_action( 'admin_menu', array( $this, 'filterable_store_admin_options' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_options_page_scripts' ) );
		add_action( 'wp_enqueue_scripts', array($this, 'register_styles_and_scripts') );
		add_shortcode( 'filterable-store', array( $this, 'render_shortcode' ) );
		add_action( 'plugins_loaded', array($this, 'wcp_load_plugin_textdomain' ) );
	}

	function wcp_load_plugin_textdomain(){
		load_plugin_textdomain( 'filterable-store', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
	}

	function filterable_store_admin_options(){
		add_submenu_page( 'edit.php?post_type=product', 'WooCommerce Filterable Store', 'Filterable Store', 'manage_options', 'filterable_store', array($this, 'render_admin_menu') );
	}

	function render_admin_menu(){
		?>
		<h2><?php _e( 'Filterable Store Shortcode Generator', 'filterable-store' ); ?></h2>
		<table class="form-table">
			<tr>
				<th><?php _e( 'Include or Exclude', 'filterable-store' ); ?></th>
				<td style="width: 40%;">
					<select class="widefat the_chosen shortcode_select">
						<option value="include"><?php _e( 'Include Following', 'filterable-store' ); ?></option>
						<option value="exclude"><?php _e( 'Exclude Following', 'filterable-store' ); ?></option>
					</select>
				</td>
				<td><?php _e( 'Include or Exclude following categories from shop', 'filterable-store' ); ?></td>
			</tr>
			<tr>
				<th><?php _e( 'Categories', 'filterable-store' ); ?></th>
				<td>
					<?php $product_categories = get_terms( 'product_cat'); ?>
					<select class="widefat shortcode_ids" multiple="">
						<?php foreach ($product_categories as $cat) {
							echo '<option value="'.$cat->term_id.'">'.$cat->name.'</option>';
						} ?>
					</select>
				</td>
				<td><?php _e( 'Select Categories you want to exclude or include', 'filterable-store' ); ?>.</td>
			</tr>
			<tr>
				<th><?php _e( 'Shortcode', 'filterable-store' ); ?></th>
				<td>
					<input type="text" style="color: green;text-align: center;" value="[filterable-store]" class="widefat shortcode_ready" disabled="disabled">
				</td>
				<td><?php _e( 'Copy and use this shortcode', 'filterable-store' ); ?>.</td>
			</tr>	
		</table>
		<?php
	}

	function admin_options_page_scripts($slug){
		if ($slug == 'product_page_filterable_store') {
			wp_enqueue_script( 'select2-js', plugin_dir_url( __FILE__ ) . 'js/select2.min.js', array('jquery'));
			wp_enqueue_style( 'select2-css', plugin_dir_url( __FILE__ ) . 'css/select2.min.css');
			wp_enqueue_script( 'wcp-shortcode-generator', plugin_dir_url( __FILE__ ) . 'js/admin.js', array('jquery'));
		}
	}

	function register_styles_and_scripts(){

		wp_register_style( 'wcp-component', plugin_dir_url( __FILE__ ) . 'css/component.css' );
		wp_register_style( 'wcp-flickity', plugin_dir_url( __FILE__ ) . 'css/flickity.css' );
		wp_register_style( 'font-awesome', plugin_dir_url( __FILE__ ) . 'css/font-awesome/css/font-awesome.min.css' );
		wp_register_script( 'wcp-isotope-js', plugin_dir_url( __FILE__ ) . 'js/isotope.pkgd.min.js' );
		wp_register_script( 'wcp-flickity-js', plugin_dir_url( __FILE__ ) . 'js/flickity.pkgd.min.js' );
		wp_register_script( 'wcp-main-js', plugin_dir_url( __FILE__ ) . 'js/main.js' );
		wp_register_script( 'wcp-modernizr-js', plugin_dir_url( __FILE__ ) . 'js/modernizr.custom.js' );
	}

	function render_shortcode($atts){

		wp_enqueue_script( 'wcp-modernizr-js' );
		wp_enqueue_script( 'wcp-isotope-js' );
		wp_enqueue_script( 'wcp-flickity-js' );
		wp_enqueue_script( 'wcp-main-js' );

		wp_enqueue_style( 'font-awesome' );
		wp_enqueue_style( 'wcp-component' );
		wp_enqueue_style( 'wcp-flickity' );

		$term_args = array();

		$query_args = array(
            'posts_per_page' => -1,
            'post_type' => 'product',
            'orderby' => 'rand',
        );
		$color = '#24252a';
        if (isset($atts['color'])) {
        	$color = $atts['color'];
        }

		if (isset($atts['include'])) {
			$include = explode(",",$atts['include']);
			$term_args = array(
				'include' => $include,
			);
			$query_args = array(
			    'posts_per_page' => -1,
			    'tax_query' => array(
			        'relation' => 'AND',
			        array(
			            'taxonomy' => 'product_cat',
			            'field' => 'id',
			            'terms' => $include
			        )
			    ),
			    'post_type' => 'product',
			    'orderby' => 'rand,'
			);
		} else if (isset($atts['exclude']))  {
			$exclude = explode(",",$atts['exclude']);
			$term_args = array(
				'exclude' => $exclude,
			);
			$query_args = array(
	            'posts_per_page' => -1,
	            'tax_query' => array(
	                'relation' => 'AND',
	                array(
	                    'taxonomy' => 'product_cat',
	                    'field' => 'id',
	                    'terms' => $exclude,
	                    'operator' => 'NOT IN'
	                )
	            ),
	            'post_type' => 'product',
	            'orderby' => 'rand,'
	        );			
		}        	
		ob_start();
	?>
	<style>
		.wcp-container .slider, .wcp-container .bar {background: <?php echo $color; ?>;}
	</style>
	<div class="wcp-container">
	<!-- Bottom bar with filter and cart info -->
	<div class="bar">
		<div class="filter">
			<span class="filter__label"><?php _e( 'Filter', 'filterable-store' ); ?>: </span>
			<button class="action filter__item filter__item--selected" data-filter="*"><?php _e( 'All', 'filterable-store' ); ?></button>
			<?php	

			$allCategories = get_terms( 'product_cat', $term_args );
			foreach ($allCategories as $cat) {
				$thumbnail_id = get_woocommerce_term_meta( $cat->term_id, 'thumbnail_id', true );
				$image = wp_get_attachment_url( $thumbnail_id );
				?>
				<button class="action filter__item" data-filter=".filter-<?php echo $cat->term_id; ?>"><i class="icon"><?php if ( $image ) {echo '<img src="' . $image . '" alt="'.$cat->name.'" width="40px" />';} else { echo $cat->name; } ?></i><span class="action__text"><?php echo $cat->name; ?></span></button>
				
				<?php
			}
			?>
		</div>
		<a href="<?php echo WC()->cart->get_cart_url(); ?>" title="<?php _e( 'View your shopping cart' ); ?>"><button class="cart">
			<i class="cart__icon fa fa-shopping-cart"></i>
			<span class="text-hidden">Shopping cart</span>
			<span class="cart__count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
		</button></a>
	</div>
	<!-- Main view -->
	<div class="view">
		
		<!-- Grid -->
		<section class="grid grid--loading">
			<!-- Loader -->
			<img class="grid__loader" src="<?php echo plugin_dir_url( __FILE__ ); ?>images/grid.svg" width="60" alt="Loader image" />
			<!-- Grid sizer for a fluid Isotope (Masonry) layout -->
			<div class="grid__sizer"></div>
			<!-- Grid items -->
			<?php

		        $products = new WP_Query( $query_args );
		        while ( $products->have_posts() ) {
		            $products->the_post();
		            $_product = wc_get_product( get_the_id() );
					$term_list = wp_get_post_terms(get_the_id(), 'product_cat', array("fields" => "ids"));		            
		            ?>
					<div class="grid__item <?php if($_product->get_average_rating() == 5 ) echo 'grid__item--size-a'; ?> <?php foreach ($term_list as $term_id) {
						?> filter-<?php echo $term_id;
					} ?>">
						<div class="slider">
							<div class="slider__item"><?php the_post_thumbnail( 'medium' ); ?></div>
							<?php 
								$attachment_ids = $_product->get_gallery_attachment_ids();

								if ( $attachment_ids ) {

									foreach ( $attachment_ids as $attachment_id ) {
										$image_link = wp_get_attachment_url( $attachment_id );

										if ( ! $image_link )
											continue;

										$image = wp_get_attachment_image( $attachment_id, 'medium' );
										?>

										<div class="slider__item"><?php echo $image; ?></div>
										<?php
									}

								}								
							?>
						</div>
						<div class="meta">
							<a href="<?php the_permalink(); ?>"><h3 class="meta__title"><?php the_title(); ?></h3></a>
							<span class="meta__brand">
								<?php if($_product->get_rating_count() == 0){
									echo 'No Reviews...'; }
								else { echo $_product->get_rating_html();} ?>
							</span>
							<span class="meta__price"><?php echo $_product->get_price_html(); ?></span>
						</div>
						<?php 
							echo apply_filters( 'woocommerce_loop_add_to_cart_link',
								sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" data-quantity="%s" class="action action--button action--buy %s product_type_%s"><i class="fa fa-shopping-cart"></i><span class="text-hidden">%s</span></a>',
									esc_url( $_product->add_to_cart_url() ),
									esc_attr( $_product->id ),
									esc_attr( $_product->get_sku() ),
									esc_attr( isset( $quantity ) ? $quantity : 1 ),
									$_product->is_purchasable() && $_product->is_in_stock() ? 'add_to_cart_button' : '',
									esc_attr( $_product->product_type ),
									esc_html( $_product->add_to_cart_text() )
								),
							$_product );
						?>						
					</div>
		            <?php
		        }		        
			?>
		</section>
		<!-- /grid-->
	</div>
	<!-- /view -->	
	</div>
	<!-- /wcp container -->	

	<?php
		return ob_get_clean();	
	}
}

?>
<?php   
/*
Plugin Name:  Cryptocurrency Widgets by Wallet.app
Plugin URI: 
Description:  Cryptocurrency Widgets WordPress plugin displays current prices of over crypto coins - bitcoin, ethereum, ripple and +4000 more. Totally free, no ads.
Author: Wallet.app
Author URI: http://wallet.app
Version: 0.1
*/

function enqueue_fe_script() {   
	wp_enqueue_script( 'marquee_widget', plugin_dir_url( __FILE__ ) . 'js/marquee_widget.js' );
	wp_enqueue_script( 'list_widget', plugin_dir_url( __FILE__ ) . 'js/list_widget.js' );
}


function enqueue_be_script() {
	wp_enqueue_script( 'list_widget', plugin_dir_url( __FILE__ ) . 'js/script.js' );
	wp_enqueue_style( 'style', plugin_dir_url( __FILE__ ) . 'css/style.css' );
}

add_action('wp_enqueue_scripts', 'enqueue_fe_script');
add_action( 'admin_enqueue_scripts', 'enqueue_be_script' );



class Wallet_App_Currency_Widget extends WP_Widget {
	// Main constructor
	public function __construct() {
		parent::__construct(
			'wallet_app_currency_widget',
			__( 'Cryptocurrency Widgets by Wallet.app', 'text_domain' ),
			array(
				'customize_selective_refresh' => true,
			)
		);
	}
	// The widget form (for the backend )
	public function form( $instance ) {
		// Set widget defaults
		$defaults = array(
			'widget_marquee'    => '',
			'bgcolor'     		=> '',
			'width'       		=> '0', 
			'coinID' 			=> isset( $instance['coinID'] ) ? wp_strip_all_tags( $instance['coinID'] ) : 'bitcoin,ethereum,ripple,eos,litecoin,bitcoin-cash,nem',
			'currencyCode'   	=> '',
		);
		
		// Parse current settings with defaults
		extract( wp_parse_args( ( array ) $instance, $defaults ) ); 
		
		?>

		<?php // Dropdown ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'widget_marquee' ); ?>"><?php _e( 'Type of Widget', 'text_domain' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'widget_marquee' ); ?>" id="<?php echo $this->get_field_id( 'widget_marquee' ); ?>" class="widefat">

			<?php
			// Your options array
			$options = array(
				'coingecko-coin-list-widget'        => __( 'Coins list widget', 'text_domain' ),
				'coingecko-coin-price-marquee-widget' => __( 'Coins marquee widget', 'text_domain' ),
			);
			// Loop through options and add each one to the select dropdown
			foreach ( $options as $key => $name ) {
				echo '<option value="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" '. selected( $widget_marquee, $key, false ) . '>'. $name . '</option>';
			} ?>
			</select>
		</p>

		<?php // Text Field ?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'bgcolor' ) ); ?>"><?php _e( 'Background color', 'text_domain' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'bgcolor' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'bgcolor' ) ); ?>" type="text" value="<?php echo esc_attr( $bgcolor ); ?>" />
		</p>

		<?php // Text Field ?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'width' ) ); ?>"><?php _e( 'Width', 'text_domain' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'width' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'width' ) ); ?>" type="number" value="<?php echo esc_attr( $width ); ?>" />
		</p>

		<?php // List area ?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'coinIDList' ) ); ?>"><?php _e( 'List of coins', 'text_domain' ); ?></label>
			<div class="wallet-app-added-coins-list" id="<?php echo $this->get_field_name('coinIDList') ?>">
				<?php 
					$curr = explode(',', $coinID);

					for($i = 0; $i < sizeof($curr); $i++)
					{
						echo '<div class="coins-list-individual-item">' . $curr[$i];
						echo '<div class="coins-list-controlls">';
						// if($i != 0)
						// {
							echo '<span data-coin-id="'.$curr[$i].'" data-coin-index='.$i.' class="dashicons dashicons-arrow-up-alt2"></span>';
						// }
						// if($i != sizeof($curr) - 1)
						// {
							echo '<span data-coin-id="'.$curr[$i].'" data-coin-index='.$i.' class="dashicons dashicons-arrow-down-alt2"></span>';
						// }
						echo '<span data-coin-id="'.$curr[$i].'" data-coin-index='.$i.' class="dashicons dashicons-no-alt"></span>';
						echo '</div></div>';
					}
				?>
			</div>
		</p>

		<?php // Text Field ?>
		<p style="display: none;">
			<label for="<?php echo esc_attr( $this->get_field_id( 'coinID' ) ); ?>"><?php _e( 'Coin IDs', 'text_domain' ); ?></label>
			<input class="widefat wallet-app-coin-id-hidden-input" id="<?php echo esc_attr( $this->get_field_id( 'coinID' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'coinID' ) ); ?>" type="text" value="<?php echo esc_attr( $coinID ); ?>" />
		</p>

		<?php
			$body = "[]";
			$response = wp_remote_get( 'http://104.248.28.171:3000/api/get-coin-list' );
			if ( is_wp_error( $response ) ){
				echo $response->get_error_message();
			}
			elseif( wp_remote_retrieve_response_code( $response ) === 200 ){
				$body = wp_remote_retrieve_body( $response );
			}
			$body = json_decode($body, true);
		?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'coin-search' ) ); ?>"><?php _e( 'Coin search', 'text_domain' ); ?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'coin-search' ) ); ?>" class="currency-search-input" name="<?php echo esc_attr( $this->get_field_name( 'coin-search' ) ); ?>" list="<?php echo esc_attr( $this->get_field_id( 'coin-search-datalist' ) ); ?>"> 
			<input type="button" value="Add" />
			<datalist id="<?php echo esc_attr( $this->get_field_id( 'coin-search-datalist' ) ); ?>">
				<?php 
					for($i = 0; $i < sizeof($body); $i++)
					{
						echo '<option value="' . esc_attr( $body[$i]['id'] ) . '">' . $body[$i]['name'] . ' ' .$body[$i]['symbol'].'</option>';
					}
				?>
			</datalist>
		</p>

		<?php // Dropdown ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'currencyCode' ); ?>"><?php _e( 'Currency Code', 'text_domain' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'currencyCode' ); ?>" id="<?php echo $this->get_field_id( 'currencyCode' ); ?>" class="widefat">
			<?php
			// Your options array
			$options = array(
				'BTC' => __( 'BTC', 'text_domain' ),
				'ETH' => __( 'ETH', 'text_domain' ),
				'USD' => __( 'USD', 'text_domain' ),
				'EUR' => __( 'EUR', 'text_domain' ),
			);
			// Loop through options and add each one to the select dropdown
			foreach ( $options as $key => $name ) {
				echo '<option value="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" '. selected( $currencyCode, $key, false ) . '>'. $name . '</option>';
			} ?>
			</select>
		</p>

	<?php }
	// Update widget settings
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['widget_marquee']    = isset( $new_instance['widget_marquee'] ) ? wp_strip_all_tags( $new_instance['widget_marquee'] ) : '';

		$instance['bgcolor']     = isset( $new_instance['bgcolor'] ) ? wp_strip_all_tags( $new_instance['bgcolor'] ) : '';
		
		$instance['width']     = isset( $new_instance['width'] ) ? wp_strip_all_tags( $new_instance['width'] ) : '';

		$instance['coinID']     = isset( $new_instance['coinID'] ) ? wp_strip_all_tags( $new_instance['coinID'] ) : '';

		$instance['currencyCode']   = isset( $new_instance['currencyCode'] ) ? wp_strip_all_tags( $new_instance['currencyCode'] ) : '';

		return $instance;
	}
	// Display the widget
	public function widget( $args, $instance ) {
		extract( $args );

		$widget_marquee    = isset( $instance['widget_marquee'] ) ? $instance['widget_marquee']: '';
		$bgcolor     = isset( $instance['bgcolor'] ) ? $instance['bgcolor'] : '';
		$width     = isset( $instance['width'] ) ? $instance['width'] : '';
		$coinID     = isset( $instance['coinID'] ) ? $instance['coinID'] : '';
		$currencyCode   = isset( $instance['currencyCode'] ) ? $instance['currencyCode'] : '';

		echo $before_widget;

		$widthText = $widget_marquee == 'coin-price-marquee-widget' ? 'width="'.$width.'"' : '';

		if($widget_marquee == 'coingecko-coin-price-marquee-widget')
		{
			echo '<span style="color: #fff; background-color: #000; padding: 3px; font-weight: 600; ">'.strtoupper($currencyCode).'</span>';
		}
		echo '<'.$widget_marquee.'  coin-ids="'.$coinID.'" currency="'.$currencyCode.'" locale="en" background-color="'.$bgcolor.'"'.$$widthText.'"/>';

		


		echo $after_widget;
	}
}
// Register the widget
function register_currencies_widget() {
	register_widget( 'Wallet_App_Currency_Widget' );
}
add_action( 'widgets_init', 'register_currencies_widget' );

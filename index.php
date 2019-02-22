<?php   
/*
Plugin Name:  Cryptocurrency Widgets by Wallet.app
Plugin URI: 
Description:  Cryptocurrency Widgets WordPress plugin displays current prices of over crypto coins - bitcoin, ethereum, ripple and +4000 more. Totally free, no ads.
Author: Wallet.app
Author URI: https://win.win
Version: 0.1
*/

function enqueue_script() {   
	wp_enqueue_script( 'marquee_widget', plugin_dir_url( __FILE__ ) . 'js/marquee_widget.js' );
	wp_enqueue_script( 'list_widget', plugin_dir_url( __FILE__ ) . 'js/list_widget.js' );
}
add_action('wp_enqueue_scripts', 'enqueue_script');



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
			'coinID' 			=> array('bitcoin', 'ethereum', 'ripple', 'eos', 'litecoin','bitcoin-cash', 'nem'),
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
				'twins-coin-list-widget'        => __( 'Coins list widget', 'text_domain' ),
				'coin-price-marquee-widget' => __( 'Coins marquee widget', 'text_domain' ),
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

		<?php // Dropdown ?>
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
			<label for="<?php echo $this->get_field_id( 'coinID' ); ?>"><?php _e( 'Currencies to show', 'text_domain' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'coinID' ); ?>[]" id="<?php echo $this->get_field_id( 'coinID' ); ?>" multiple="multiple" class="widefat">
			<?php
			// Your options array
			
			$selected = '';
			$defaults = array('bitcoin', 'ethereum', 'ripple', 'eos', 'litecoin','bitcoin-cash', 'nem');
			for($i = 0; $i < sizeof($body); $i++)
			{
				$selected = in_array(esc_attr( $body[$i]['id'] ), $coinID) || in_array(esc_attr( $body[$i]['id']), $defaults)? 'selected' : '';
				echo '<option value="' . esc_attr( $body[$i]['id'] ) . '" id="' . esc_attr( $body[$i]['id'] ) . '" '.$selected. '>'. $body[$i]['name'] . '</option>';
			}

			// Loop through options and add each one to the select dropdown
			/* foreach ( $options as $key => $name ) {
				$selected = in_array(esc_attr( $key ), $coinID) ? 'selected' : '';
				echo '<option value="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" '.$selected. '>'. $name . '</option>';
			} */ ?>
			</select>
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

		$coinID = ( !empty($new_instance['coinID']) ) ? (array) $new_instance['coinID'] : array();
	    $instance['coinID'] = array_map( 'sanitize_text_field', $coinID );

		$instance['currencyCode']   = isset( $new_instance['currencyCode'] ) ? wp_strip_all_tags( $new_instance['currencyCode'] ) : '';

		return $instance;
	}
	// Display the widget
	public function widget( $args, $instance ) {
		extract( $args );

		$widget_marquee    = isset( $instance['widget_marquee'] ) ? $instance['widget_marquee']: '';
		$bgcolor     = isset( $instance['bgcolor'] ) ? $instance['bgcolor'] : '';
		$width     = isset( $instance['width'] ) ? $instance['width'] : '';
		$coinID = isset( $instance['coinID'] ) ?$instance['coinID'] : '';
		$currencyCode   = isset( $instance['currencyCode'] ) ? $instance['currencyCode'] : '';

		echo $before_widget;

		$widthText = $widget_marquee == 'coin-price-marquee-widget' ? 'width="'.$width.'"' : '';

		echo '<'.$widget_marquee.'  coin-ids="'.implode(',', $coinID).'" currency="'.$currencyCode.'" locale="en" background="'.$bgcolor.'"'.$$widthText.'"/>';

		echo $after_widget;
	}
}
// Register the widget
function register_currencies_widget() {
	register_widget( 'Wallet_App_Currency_Widget' );
}
add_action( 'widgets_init', 'register_currencies_widget' );
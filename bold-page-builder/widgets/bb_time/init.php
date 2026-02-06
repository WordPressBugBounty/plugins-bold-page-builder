<?php

if ( ! class_exists( 'BB_Time_Widget' ) ) {

	// TIME

	class BB_Time_Widget extends WP_Widget {

		function __construct() {
			parent::__construct(
				'bt_bb_time_widget', // Base ID
				esc_html__( 'BB Time', 'bold-builder' ), // Name
				array( 'description' => esc_html__( 'Time widget.', 'bold-builder' ) ) // Args
			);
		}

		public function widget( $args, $instance ) {
			
			wp_enqueue_script( 'moment' );
			wp_enqueue_script( 'bt_bb_moment_timezone', plugin_dir_url( __FILE__ ) . 'moment-timezone-with-data.js', array(), BT_BB_VERSION, true );
			
			$this->container_id = uniqid( 'time' );
			
			$this->icon = ! empty( $instance['icon'] ) ? $instance['icon'] : 'fa_f017';
			$this->time_zone = ! empty( $instance['time_zone'] ) ? $instance['time_zone'] : '';
			$this->place_name = ! empty( $instance['place_name'] ) ? $instance['place_name'] : '';
			$this->time_notation = ! empty( $instance['time_notation'] ) ? $instance['time_notation'] : '';
			
			$proxy = new BB_Time_Widget_Proxy( $this->time_zone, $this->place_name, $this->time_notation, $this->container_id );
			add_action( 'wp_footer', array( $proxy, 'js' ) );
			
			//echo $args['before_widget']; // TODO: fix CSS in themes
			
			// $this->icon already escaped in get_html, can not escape here without breaking custom icons and backward compatibility (custom data-ico-... attribute, custom css based on it)
			echo '<span id="' . esc_attr( $this->container_id ) . '" class="btIconWidget"><span class="btIconWidgetIcon">' . bt_bb_icon::get_html( $this->icon ) . '</span><span class="btIconWidgetContent"><span class="btIconWidgetTitle">' . esc_html( $this->place_name ) . '</span><span class="btIconWidgetText"></span></span></span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			
			//echo $args['after_widget']; // TODO: fix CSS in themes
		}

		public function form( $instance ) {
			$icon = ! empty( $instance['icon'] ) ? $instance['icon'] : '';
			$time_zone = ! empty( $instance['time_zone'] ) ? $instance['time_zone'] : '';
			$place_name = ! empty( $instance['place_name'] ) ? $instance['place_name'] : '';
			$time_notation = ! empty( $instance['time_notation'] ) ? $instance['time_notation'] : '';

			?>		
			<div class="bt_bb_iconpicker_widget_container">
				<label for="<?php echo esc_attr( $this->get_field_id( 'icon' ) ); ?>"><?php esc_html_e( 'Icon:', 'bold-builder' ); ?></label>
				<input type="hidden" id="<?php echo esc_attr( $this->get_field_id( 'icon' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'icon' ) ); ?>" value="<?php echo esc_attr( $icon ); ?>">
				<input type="hidden" name="<?php echo esc_attr( $this->get_field_name( 'bt_bb_iconpicker' ) ); ?>">
				<div class="bt_bb_iconpicker_widget_placeholder" data-icon="<?php echo esc_attr( $icon ); ?>"></div>
			</div>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'time_zone' ) ); ?>"><?php esc_html_e( 'Time zone:', 'bold-builder' ); ?></label> 
				<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'time_zone' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'time_zone' ) ); ?>">
					<?php
					
					require_once( 'helper.php' );
					
					$tz = bt_bb_time_zone();

					foreach ( $tz as $item ) {
						if ( $item == $time_zone ) {
							echo '<option value="' . esc_attr( $item ) . '" selected>' . esc_html( $item ) . '</option>';
						} else {
							echo '<option value="' . esc_attr( $item ) . '">' . esc_html( $item ) . '</option>';
						}
					}
					?>
				</select>
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'place_name' ) ); ?>"><?php esc_html_e( 'Place name:', 'bold-builder' ); ?></label> 
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'place_name' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'place_name' ) ); ?>" type="text" value="<?php echo esc_attr( $place_name ); ?>">
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'time_notation' ) ); ?>"><?php esc_html_e( 'Time notation:', 'bold-builder' ); ?></label> 
				<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'time_notation' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'time_notation' ) ); ?>">
					<?php
					
					require_once( 'helper.php' );
					
					$tn = array( esc_html__( '24 hours', 'bold-builder' ) => '24', esc_html__( '12 hours', 'bold-builder' ) => '12' );

					foreach ( $tn as $k => $v ) {
						if ( $v == $time_notation ) {
							echo '<option value="' . esc_attr( $v ) . '" selected>' . esc_html( $k ) . '</option>';
						} else {
							echo '<option value="' . esc_attr( $v ) . '">' . esc_html( $k ) . '</option>';
						}
					}
					?>
				</select>
			</p>
			<?php 
		}

		public function update( $new_instance, $old_instance ) {
			$instance = array();
			$instance['icon'] = ( ! empty( $new_instance['icon'] ) ) ? strip_tags( $new_instance['icon'] ) : '';
			$instance['time_zone'] = ( ! empty( $new_instance['time_zone'] ) ) ? strip_tags( $new_instance['time_zone'] ) : '';
			$instance['place_name'] = ( ! empty( $new_instance['place_name'] ) ) ? strip_tags( $new_instance['place_name'] ) : '';
			$instance['time_notation'] = ( ! empty( $new_instance['time_notation'] ) ) ? strip_tags( $new_instance['time_notation'] ) : '';

			return $instance;
		}
	}
	
	class BB_Time_Widget_Proxy {
		public $time_zone;
		public $place_name;
		public $time_notation;
		public $container_id;
		function __construct( $time_zone, $place_name, $time_notation, $container_id ) {
			$this->time_zone = $time_zone;
			$this->place_name = $place_name;
			$this->time_notation = $time_notation;
			$this->container_id = $container_id;
		}
		public function js() { ?>
			<script>
				(function( $ ) {
					$( document ).ready(function() {
						
						var time_notation = '<?php echo esc_js( $this->time_notation ); ?>';
						
						var time = function() {
							
							if ( time_notation == '12' ) {
								var time = moment().tz( '<?php echo esc_js( $this->time_zone ); ?>' ).format( 'h:mm A' );
							} else {
								var time = moment().tz( '<?php echo esc_js( $this->time_zone ); ?>' ).format( 'H:mm' );
							}

							$( '#<?php echo esc_js( $this->container_id ); ?> .btIconWidgetText' ).html( time );
						}
						setInterval( function() {
							time();
						}, 1000 );
						time();
					});
				})( jQuery );
			</script>
		<?php }		
	}
}
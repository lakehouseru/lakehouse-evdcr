<?php
/*
Plugin Name: User Locker
Plugin URI: http://www.poradnik-webmastera.com/projekty/user_locker/
Description: This plugin locks user account after given number of incorrect login attempts.
Author: Daniel Frużyński
Version: 1.2
Author URI: http://www.poradnik-webmastera.com/
Text Domain: user-locker
License: GPL2
*/

/*  Copyright 2009-2011  Daniel Frużyński  (email : daniel [A-T] poradnik-webmastera.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


if ( !class_exists( 'UserLocker' ) || ( defined( 'WP_DEBUG' ) && WP_DEBUG  ) ) {

class UserLocker {
	var $prev_lock_status = null;
	var $locked = false;
	
	// Constructor
	function UserLocker() {
		// Initialise plugin
		add_action( 'init', array( &$this, 'init' ) );
		add_action( 'admin_init', array( &$this, 'admin_init' ) );
		
		// Add option to Admin menu
		add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
		
		// Check if user is already locked
		add_filter( 'wp_authenticate_user', array( &$this, 'wp_authenticate_user' ), 1 );
		
		// Set password check flag
		add_filter( 'check_password', array( &$this, 'check_password' ) );

		// Increment bad attempt counter and finally lock account
		add_action( 'wp_login_failed', array( &$this, 'wp_login_failed' ) );
		
		// Reset account lock on pass reset and valid login
		add_action( 'password_reset', array( &$this, 'password_reset' ) );
		add_action( 'wp_login', array( &$this, 'wp_login' ) );
		
		// Add info about account lock
		add_filter( 'login_errors', array( &$this, 'login_errors' ) );
		
		// Edit user profile
		add_action( 'edit_user_profile', array( &$this, 'edit_user_profile' ) );
		add_action( 'edit_user_profile_update', array( &$this, 'edit_user_profile_update' ) );
		
		// Add new column to the user list
		add_filter( 'manage_users_columns', array( &$this, 'manage_users_columns' ) );
		add_filter( 'manage_users_custom_column', array( &$this, 'manage_users_custom_column' ), 10, 3 );
	}
	
	// Initialise plugin
	function init() {
		load_plugin_textdomain( 'user-locker', false, dirname( plugin_basename( __FILE__ ) ).'/lang' );
	}
	
	// Initialise plugin - admin part
	function admin_init() {
		register_setting( 'user-locker', 'userlocker_max_attempts', array( &$this, 'sanitize_nonnegative' ) );
		register_setting( 'user-locker', 'userlocker_default_lock_reason', 'trim' );
		register_setting( 'user-locker', 'userlocker_show_reason', array( &$this, 'sanitize_bool' ) );
		register_setting( 'user-locker', 'userlocker_auto_clear_reason', array( &$this, 'sanitize_bool' ) );
		register_setting( 'user-locker', 'userlocker_compact_columns', array( &$this, 'sanitize_bool' ) );
	}
	
	// Add option to Admin menu
	function admin_menu() {
		add_submenu_page( 'options-general.php', 'User Locker', 
			'User Locker', 'manage_options', __FILE__, array( &$this, 'options_panel' ) );
	}
	
	// Check if user is already locked
	function wp_authenticate_user( $user ) {
		if ( is_wp_error( $user ) ) {
			return $user;
		}
		
		// Return error if user account is disabled
		$disabled = get_user_option( 'ul_disabled', $user->ID, false );
		if ( $disabled ) {
			$reason = '';
			if ( get_option( 'userlocker_show_reason' ) ) {
				$reason = (string)get_user_option( 'ul_disable_reason', $user->ID, false );
				if ( ( strlen( $reason ) > 0 ) && ( $reason[0] == '@' ) ) {
					$reason = '';
				}
			}
			
			if ( $reason == '' ) {
				return new WP_Error( 'ul_user_disabled', __('<strong>ERROR</strong>: This user account is disabled.', 'user-locker') );
			} else {
				return new WP_Error('ul_user_disabled', sprintf( __('<strong>ERROR</strong>: This user account is disabled (reason: %s).', 'user-locker'), esc_html( $reason ) ) );
			}
		}
		
		// Return error if user account is locked
		$locked = get_user_option( 'ul_locked', $user->ID, false );
		if ( $locked ) {
			$reason = '';
			if ( get_option( 'userlocker_show_reason' ) ) {
				$reason = (string)get_user_option( 'ul_lock_reason', $user->ID, false );
				if ( ( strlen( $reason ) > 0 ) && ( $reason[0] == '@' ) ) {
					$reason = '';
				}
			}
			
			if ( $reason == '' ) {
				return new WP_Error( 'ul_user_locked', __('<strong>ERROR</strong>: This user account is locked for security reasons. Please use Lost Password option to unlock it.', 'user-locker') );
			} else {
				return new WP_Error( 'ul_user_locked', sprintf( __('<strong>ERROR</strong>: This user account is locked (reason: %s). Please use Lost Password option to unlock it.', 'user-locker'), esc_html( $reason ) ) );
			}
		}
		
		return $user;
	}
	
	// Set password check flag
	function check_password( $check ) {
		if ( !is_null( $this->prev_lock_status ) ) {
			update_user_option( $this->prev_lock_status['id'], 'ul_locked', $this->prev_lock_status['status'], false );
			update_user_option( $this->prev_lock_status['id'], 'ul_bad_attempts', $this->prev_lock_status['count'], false );
			update_user_option( $this->prev_lock_status['id'], 'ul_lock_reason', $this->prev_lock_status['reason'], false );
			$this->prev_lock_status = null;
		}
		
		return $check;
	}
	
	// Increment bad attempt counter and finally lock account
	function wp_login_failed( $username ) {
		$user = get_userdatabylogin( $username );
		if ( !$user || ( $user->user_login != $username ) ) {
			// Invalid username
			return;
		}
		
		// Older WP versions called this function few times, and only last one should count. 
		// Therefore save old data now and restore it in check_password hook if needed
		$this->prev_lock_status = array(
			'id' => $user->ID,
			'status' => get_user_option( 'ul_locked', $user->ID, false ),
			'count' => get_user_option( 'ul_bad_attempts', $user->ID, false ),
			'reason' => get_user_option( 'ul_lock_reason', $user->ID, false ),
		);
		
		$disabled = get_user_option( 'ul_disabled', $user->ID, false );
		$locked = get_user_option( 'ul_locked', $user->ID, false );
		if ( !$disabled && !$locked ) {
			$cnt = get_user_option( 'ul_bad_attempts', $user->ID, false );
			if ( $cnt === false ) {
				$cnt = 1;
			} else {
				++$cnt;
			}
			update_user_option( $user->ID, 'ul_bad_attempts', $cnt, false );
			
			if ( $cnt >= get_option( 'userlocker_max_attempts' ) ) {
				$this->locked = true;
				$this->lock_user( $user->ID, get_option( 'userlocker_default_lock_reason', '' ) );
			}
		}
	}
	
	// Reset account lock on pass reset
	function password_reset( $user ) {
		$this->unlock_user( $user->ID );
	}
	
	// Reset account lock on valid login
	function wp_login( $username ) {
		$user = get_userdatabylogin( $username );
		$this->unlock_user( $user->ID );
	}
	
	// Lock account for given user
	function lock_user( $user_id, $reason = '' ) {
		// Do not touch 'ul_bad_attempts' - it needs to be updated separately
		
		$old_status = $this->is_user_locked( $user_id );
		
		// Update status
		if ( !$old_status ) {
			update_user_option( $user_id, 'ul_locked', true, false );
		}
		// Update reason
		if ( $reason !== false ) {
			update_user_option( $user_id, 'ul_lock_reason', $reason, false );
		}
		// Call hooks
		if ( !$old_status ) {
			do_action( 'user_locker_lock_user', $user_id );
		}
	}
	
	// Unlock account for given user
	function unlock_user( $user_id, $reason = false ) {
		$old_status = $this->is_user_locked( $user_id );
		
		// Update status
		if ( $old_status ) {
			update_user_option( $user_id, 'ul_bad_attempts', 0, false );
			update_user_option( $user_id, 'ul_locked', false, false );
		}
		// Update reason
		if ( get_option( 'userlocker_auto_clear_reason' ) ) {
			if ( function_exists( 'delete_user_option' ) ) { // WP3.0+
				delete_user_option( $user_id, 'ul_lock_reason' );
			} else {
				update_user_option( $user_id, 'ul_lock_reason', '', false );
			}
		} elseif ( $reason !== false ) {
			update_user_option( $user_id, 'ul_lock_reason', $reason, false );
		}
		// Call hooks
		if ( $old_status ) {
			do_action( 'user_locker_unlock_user', $user_id );
		}
	}
	
	// Disable account for given user
	function disable_user( $user_id, $reason = '' ) {
		$old_status = $this->is_user_disabled( $user_id );
		
		// Update status
		if ( !$old_status ) {
			update_user_option( $user_id, 'ul_disabled', true, false );
		}
		// Update reason
		if ( $reason !== false ) {
			update_user_option( $user_id, 'ul_disable_reason', $reason, false );
		}
		// Call hooks
		if ( !$old_status ) {
			do_action( 'user_locker_disable_user', $user_id );
		}
	}
	
	// Enable account for given user
	function enable_user( $user_id, $reason = false ) {
		$old_status = $this->is_user_disabled( $user_id );
		
		// Update status
		if ( $old_status ) {
			update_user_option( $user_id, 'ul_disabled', false, false );
		}
		// Update reason
		if ( get_option( 'userlocker_auto_clear_reason' ) ) {
			if ( function_exists( 'delete_user_option' ) ) { // WP3.0+
				delete_user_option( $user_id, 'ul_disable_reason' );
			} else {
				update_user_option( $user_id, 'ul_disable_reason', '', false );
			}
		} elseif ( $reason !== false ) {
			update_user_option( $user_id, 'ul_disable_reason', $reason, false );
		}
		// Call hooks
		if ( $old_status ) {
			do_action( 'user_locker_enable_user', $user_id );
		}
	}
	
	function is_user_locked( $user_id ) {
		return get_user_option( 'ul_locked', $user_id, false );
	}
	
	function is_user_disabled( $user_id ) {
		return get_user_option( 'ul_disabled', $user_id, false );
	}
	
	// Add info about account lock
	function login_errors( $errors ) {
		if ( $this->locked ) {
			$errors .= __('<strong>ERROR</strong>: This user account has been locked for security reasons. Please use Lost Password option to unlock it.', 'user-locker') . "<br />\n";
		}
		return $errors;
	}
	
	function edit_user_profile() {
		if ( !current_user_can( 'edit_users' ) ) {
			return;
		}
		
		global $user_id;
		
		// User cannot disable itself
		$current_user = wp_get_current_user();
		$current_user_id = $current_user->ID;
		if ( $current_user_id == $user_id ) {
			return;
		}
?>
<h3><?php _e('User Locking', 'user-locker') ?></h3>
<table class="form-table">
<tr>
	<th scope="row"><?php _e('User account locked', 'user-locker'); ?></th>
	<td><label for="ul_locked"><input name="ul_locked" type="checkbox" id="ul_locked" value="false" <?php checked(true, get_user_option( 'ul_locked', $user_id, false )); ?> /> <?php _e('User account is locked for security reasons', 'user-locker'); ?></label></td>
</tr>
<tr>
	<th scope="row"><label for="ul_lock_reason"><?php _e('Lock reason', 'user-locker'); ?></label></th>
	<td><input type="text" maxlength="500" size="80" name="ul_lock_reason" id="ul_lock_reason" value="<?php echo esc_attr( get_user_option( 'ul_lock_reason', $user_id, false ) ); ?>" /><br /><?php _e('Note: start text with \'@\' (AT sign) to keep it private.', 'user-locker'); ?></td>
</tr>
<tr>
	<th scope="row"><?php _e('User account disabled', 'user-locker'); ?></th>
	<td><label for="ul_disabled"><input name="ul_disabled" type="checkbox" id="ul_disabled" value="false" <?php checked(true, get_user_option( 'ul_disabled', $user_id, false )); ?> /> <?php _e('User account is disabled', 'user-locker'); ?></label></td>
</tr>
<tr>
	<th scope="row"><label for="ul_disable_reason"><?php _e('Disable reason', 'user-locker'); ?></label></th>
	<td><input type="text" maxlength="500" size="80" name="ul_disable_reason" id="ul_disable_reason" value="<?php echo esc_attr( get_user_option( 'ul_disable_reason', $user_id, false ) ); ?>" /><br /><?php _e('Note: start text with \'@\' (AT sign) to keep it private.', 'user-locker'); ?></td>
</tr>
</table>
<?php
	}
	
	function edit_user_profile_update() {
		if ( !current_user_can( 'edit_users' ) ) {
			return;
		}
		
		global $user_id;
		
		// User cannot disable itself
		$current_user = wp_get_current_user();
		$current_user_id = $current_user->ID;
		if ( $current_user_id == $user_id ) {
			return;
		}
		
		// Lock/unlock user
		$new_status = isset( $_POST['ul_locked'] );
		$new_reason = isset( $_POST['ul_lock_reason'] ) ? trim( $_POST['ul_lock_reason'] ) : '';
		if ( $new_status ) {
			$this->lock_user( $user_id, $new_reason );
		} else {
			$this->unlock_user( $user_id, $new_reason );
		}
		
		// Disable/enable user
		$new_status = isset( $_POST['ul_disabled'] );
		$new_reason = isset( $_POST['ul_disable_reason'] ) ? trim( $_POST['ul_disable_reason'] ) : '';
		if ( $new_status ) {
			$this->disable_user( $user_id, $new_reason );
		} else {
			$this->enable_user( $user_id, $new_reason );
		}
	}
	
	// Add new column to the user list page
	function manage_users_columns( $columns ) {
		// This requires WP 2.8+
		global $wp_version;
		if ( version_compare( $wp_version, '2.7.999', '>' ) ) {
			if ( get_option( 'userlocker_compact_columns' ) ) {
				$columns['userlocker'] = __('Locked / Disabled', 'user-locker');
			} else {
				$columns['userlocker_locked'] = __('Locked', 'user-locker');
				$columns['userlocker_disabled'] = __('Disabled', 'user-locker');
			}
		}
		return $columns;
	}
	
	// Add column content for each user on user list
	function manage_users_custom_column( $value, $column_name, $user_id ) {
		if ( $column_name == 'userlocker' ) {
			if ( get_user_option( 'ul_locked', $user_id, false ) ) {
				$reason = get_user_option( 'ul_lock_reason', $user_id, false );
				if ( empty( $reason ) ) {
					$ret = '<b>';
				} else {
					$ret = '<b title="' . esc_attr( $reason ) . '">';
				}
				$ret .= __('Yes', 'user-locker');
				if ( !empty( $reason ) ) {
					$ret .= '<sup style="font-size: smaller">*)</sup>';
				}
				$ret .= '</b>';
			} else {
				$ret = __('No', 'user-locker');
			}
			$ret .= ' / ';
			if ( get_user_option( 'ul_disabled', $user_id, false ) ) {
				$reason = get_user_option( 'ul_disable_reason', $user_id, false );
				if ( empty( $reason ) ) {
					$ret .= '<b>';
				} else {
					$ret .= '<b title="' . esc_attr( $reason ) . '">';
				}
				$ret .= __('Yes', 'user-locker');
				if ( !empty( $reason ) ) {
					$ret .= '<sup style="font-size: smaller">*)</sup>';
				}
				$ret .= '</b>';
			} else {
				$ret .= __('No', 'user-locker');
			}
			return $ret;
		} elseif ( $column_name == 'userlocker_locked' ) {
			if ( get_user_option( 'ul_locked', $user_id, false ) ) {
				$ret = '<b>' . __('Yes', 'user-locker') . '</b>';
				$reason = get_user_option( 'ul_lock_reason', $user_id, false );
				if ( !empty( $reason ) ) {
					$ret .= ' (' . esc_html( $reason ) . ')';
				}
			} else {
				$ret = __('No', 'user-locker');
			}
			return $ret;
		} elseif ( $column_name == 'userlocker_disabled' ) {
			if ( get_user_option( 'ul_disabled', $user_id, false ) ) {
				$ret = '<b>' . __('Yes', 'user-locker') . '</b>';
				$reason = get_user_option( 'ul_disable_reason', $user_id, false );
				if ( !empty( $reason ) ) {
					$ret .= ' (' . esc_html( $reason ) . ')';
				}
			} else {
				$ret = __('No', 'user-locker');
			}
			return $ret;
		}
		
		return $value;
	}
	
	function sanitize_nonnegative( $value ) {
		$value = (int)$value;
		if ( $value < 0 ) {
			$value = 0;
		}
		return $value;
	}
	
	function sanitize_bool( $value ) {
		if ( ( $value == 'yes' ) || ( (int)$value != 0 ) ) {
			return true;
		} else {
			return false;
		}
	}
	
	// Display settings form
	function options_panel() {
?>
<div class="wrap">
<?php screen_icon(); ?>
<h2><?php _e('User Locker - Options', 'user-locker'); ?></h2>

<form name="dofollow" action="options.php" method="post">
<?php settings_fields( 'user-locker' ); ?>
<table class="form-table">

<tr><th colspan="2"><h3><?php _e('Account locking:', 'user-locker'); ?></h3></th></tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="userlocker_max_attempts"><?php _e('Maximum invalid login attempts before account locking:', 'user-locker'); ?></label>
</th>
<td>
<input type="text" maxlength="6" size="10" id="userlocker_max_attempts" name="userlocker_max_attempts" value="<?php echo stripcslashes( get_option( 'userlocker_max_attempts' ) ); ?>" />
</td>
</tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="userlocker_default_lock_reason"><?php _e('Default lock reason:', 'user-locker'); ?></label>
</th>
<td>
<input type="text" maxlength="500" size="80" id="userlocker_default_lock_reason" name="userlocker_default_lock_reason" value="<?php echo esc_attr( get_option( 'userlocker_default_lock_reason' ) ); ?>" />
<br /><?php _e('Note: start text with \'@\' (AT sign) to keep it private.', 'user-locker'); ?>
</td>
</tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="userlocker_show_reason"><?php _e('Show account lock/disable reason after login attempt:', 'user-locker'); ?></label>
</th>
<td>
<input type="checkbox" id="userlocker_show_reason" name="userlocker_show_reason" value="yes" <?php checked( true, get_option( 'userlocker_show_reason' ) ); ?> />
</td>
</tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="userlocker_auto_clear_reason"><?php _e('Clean lock/disable reason when user is unlocked/enabled:', 'user-locker'); ?></label>
</th>
<td>
<input type="checkbox" id="userlocker_auto_clear_reason" name="userlocker_auto_clear_reason" value="yes" <?php checked( true, get_option( 'userlocker_auto_clear_reason' ) ); ?> />
</td>
</tr>

<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<label for="userlocker_compact_columns"><?php _e('Show single status column:', 'user-locker'); ?></label>
</th>
<td>
<input type="checkbox" id="userlocker_compact_columns" name="userlocker_compact_columns" value="yes" <?php checked( true, get_option( 'userlocker_compact_columns' ) ); ?> /><br /><?php _e('Add one column instead of two to the User List, and show lock/disable reason in tooltip only.', 'user-locker'); ?>
</td>
</tr>

</table>

<p class="submit">
<input type="submit" name="Submit" value="<?php _e('Save settings', 'user-locker'); ?>" /> 
</p>

</form>
</div>
<?php
	}
}

add_option( 'userlocker_max_attempts', 5 ); // Maximum invalid login attempts before account locking
add_option( 'userlocker_default_lock_reason', '' ); // Default lock reason
add_option( 'userlocker_show_reason', false ); // Show lock/disable reason to end user
add_option( 'userlocker_auto_clear_reason', false ); // Clear lock/disable reason during unlocking/enabling
add_option( 'userlocker_compact_columns', true ); // Show single column and reasons in tooltips

$wp_user_locker = new UserLocker();

// Add functions from WP2.8 for previous WP versions
if ( !function_exists( 'esc_html' ) ) {
	function esc_html( $text ) {
		return wp_specialchars( $text );
	}
}

if ( !function_exists( 'esc_attr' ) ) {
	function esc_attr( $text ) {
		return attribute_escape( $text );
	}
}


// Public functions - use them for integration with User Locker plugin

/**
 * Lock user account (user may unlock it by requesting new password)
 *
 * @since 1.2
 * @uses apply_filters() Calls 'user_locker_lock_user' on user id.
 *
 * @param $user_id int User ID
 * @param $reason bool|string New lock reason (may be empty string) or False to do not update lock reason. Default empty string
 */
function user_locker_lock_user( $user_id, $reason = '' ) {
	global $wp_user_locker;
	$wp_user_locker->lock_user( $user_id, $reason );
}

/**
 * Unlock user account
 *
 * @since 1.2
 * @uses apply_filters() Calls 'user_locker_unlock_user' on user id.
 *
 * @param $user_id int User ID
 * @param $reason bool|string New lock reason (may be empty string) or False to do not update lock reason. Default false
 */
function user_locker_unlock_user( $user_id, $reason = false ) {
	global $wp_user_locker;
	$wp_user_locker->unlock_user( $user_id, $reason );
}

/**
 * Disable user account (user cannot enable it, only admin can do this)
 *
 * @since 1.2
 * @uses apply_filters() Calls 'user_locker_enable_user' on user id.
 *
 * @param $user_id int User ID
 * @param $reason bool|string New disable reason (may be empty string) or False to do not update disable reason. Default empty string
 */
function user_locker_disable_user( $user_id, $reason = '' ) {
	global $wp_user_locker;
	$wp_user_locker->disable_user( $user_id, $reason );
}

/**
 * Enable user account
 *
 * @since 1.2
 * @uses apply_filters() Calls 'user_locker_enable_user' on user id.
 *
 * @param $user_id int User ID
 * @param $reason bool|string New disable reason (may be empty string) or False to do not update disable reason. Default false
 */
function user_locker_enable_user( $user_id, $reason = false ) {
	global $wp_user_locker;
	$wp_user_locker->enable_user( $user_id, $reason );
}

/**
 * Get account lock status for user
 *
 * @since 1.2
 *
 * @param $user_id int User ID
 * @return bool true when account is locked, false otherwise
 */
function user_locker_is_user_locked( $user_id ) {
	global $wp_user_locker;
	return $wp_user_locker->is_user_locked( $user_id );
}

/**
 * Get account disable status for user
 *
 * @since 1.2
 *
 * @param $user_id int User ID
 * @return bool true when account is disabled, false otherwise
 */
function user_locker_is_user_disabled( $user_id ) {
	global $wp_user_locker;
	return $wp_user_locker->is_user_disabled( $user_id );
}

} // END

?>
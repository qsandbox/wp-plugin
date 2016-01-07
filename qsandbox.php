<?php
/*
Plugin Name: qSandbox
Plugin URI: http://club.orbisius.com/products/wordpress-plugins/qsandbox/
Description: Facilitates the communication with qSandbox and some cool stuff.
Version: 1.0.0
Author: Svetoslav Marinov (Slavi) | qSandbox.com
Author URI: http://qSandbox.com
*/

/*  Copyright 2012-2200 Svetoslav Marinov (Slavi) <slavi@orbisius.com>

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define( 'QSANDBOX_PLUGIN_FILE', __FILE__ );

require_once( dirname( __FILE__ ) . '/lib/bootstrap.php' );
require_once( dirname( __FILE__ ) . '/lib/widgets.php' );
require_once( dirname( __FILE__ ) . '/lib/api.php' ); // should I check for is admin ?
require_once( dirname( __FILE__ ) . '/lib/util.php' ); // should I check for is admin ?
require_once( dirname( __FILE__ ) . '/lib/admin.php' ); // should I check for is admin ?
require_once( dirname( __FILE__ ) . '/lib/result.php' ); // should I check for is admin ?



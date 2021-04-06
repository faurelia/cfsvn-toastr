<?php

defined( 'ABSPATH' ) or die( 'Access denied' );

/**
 * Plugin Name:       Contact Form Toast Message
 * Plugin URI:        https://mangoco.de
 * Description:       Change Contact Form 7 AJAX response to toast message.
 * Version:           1.0.0
 * Author:            Mango CODE
 * Author URI:        https://github.com/faurelia
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       cfsvn-toastr
 * 
 * Contact Form Toast Message is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 
 * Contact Form Toast Message is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 
 * You should have received a copy of the GNU General Public License
 * along with Contact Form Toast Message. If not, see {URI to Plugin License}.
 */

class cfsvn_toastr {

    public function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'ct_enqueue_scripts' ) );
        add_action( 'wp_head', array( $this, 'cfsvn_toastr_head' ) );
        add_action( 'wp_footer', array( $this, 'cfsvn_toastr_footer' ) );
    }

    public function ct_enqueue_scripts() {
        wp_enqueue_script( 'toastr', '//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js', array( ) );
        wp_enqueue_style( 'toastr', '//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css' );
    
        wp_localize_script( 'toastr', 'cfsvn_toastr', [ 'is_rtl' => intval( is_rtl() ) ] );
    }

    
    public function cfsvn_toastr_head() {

        ?>
            <style type="text/css">
            .wpcf7-not-valid-tip, .wpcf7-response-output {
                display: none;
            }
            .wpcf7-form-control.wpcf7-not-valid {
                outline: 2px solid red;
            }
            </style>

        <?php
    }

    public function cfsvn_toastr_footer() {

    ?>
        <script type="application/javascript">
            (function($){
                $(document).ready( function() {
                    var contactform = $('.wpcf7');
                    
                    if ( ! contactform.length ) {
                        return;
                    }

                    contactform.on( 'submit', function (e) {
                        $(this).find('[type="submit"]').prop('disabled', true);
                    });
                    
                    contactform.on( 'wpcf7submit', function (e) {
                        $(this).find('[type="submit"]').prop('disabled', false);

                        toastr.options.rtl = cfsvn_toastr.is_rtl === 1;

                        if ( typeof e.detail === "undefined" ) {
                            alert('We cannot process this request at the moment. Sorry for the inconvenience.');
                            return;
                        }

                        var response = e.detail.apiResponse;
                        if ( response.status == 'mail_sent' ) {
                            toastr.success(response.message, 'Success')
                        } else {
                            toastr.error(response.message, 'Error');
                        }
                    } );
                } );

            })(jQuery);
        </script>
    <?php

    }
}

new cfsvn_toastr();

<?php
add_filter( 'plugin_row_meta', 'usw_userway_custom_links', 10, 2 );
function usw_userway_custom_links( $links, $file ) {
    if ( strpos( $file, 'userway.php' ) !== false ) {
        $links[] = '<a href="https://manage.userway.org/">Upgrade to Pro</a>';
        $links[] = '<a href="../wp-admin/admin.php?page=userway">Go to Dashboard</a>';
    }
    return $links;
}

function usw_userway_enqueue_styles() {
    // Register the stylesheet
    wp_register_style(
        'usw-userway-styles', // Handle
        plugins_url('assets/css/style.css', __FILE__) // Path to the stylesheet
    );

    // Enqueue the stylesheet
    wp_enqueue_style('usw-userway-styles');
}

add_action('admin_enqueue_scripts', 'usw_userway_enqueue_styles');

// Hook into plugin activation to add custom admin notice
function usw_userway_activation_notice() {
    add_option( 'usw_userway_activation_notice', true );
}

// Hook into admin_init to display the notice
function usw_userway_admin_notice() {
    global $pagenow;
    // Generate the URL for the image
    $image_url = plugins_url('assets/images/arrow-right.png', __FILE__);
    if ( $pagenow === 'plugins.php' && get_option( 'usw_userway_activation_notice' ) && ! isset( $_COOKIE['usw_userway_activation_notice_dismissed'] ) ) {
        ?>
        <div class="notice notice-warning is-dismissible" id="plugin-activation-notice">
 		    <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTI1IiBoZWlnaHQ9IjIwIiB2aWV3Qm94PSIwIDAgMTI1IDIwIiBmaWxsPSJub25lIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgo8cGF0aCBmaWxsLXJ1bGU9ImV2ZW5vZGQiIGNsaXAtcnVsZT0iZXZlbm9kZCIgZD0iTTkuOTkzNyAxNi40NDQyQzYuNDQyNjEgMTYuNDQ0MiAzLjU1MzU5IDEzLjU1NDIgMy41NTM1OSAxMFY0LjY2NjY3QzMuNTUyNzEgNC4wMTg3IDMuMjk1MDggMy4zOTc1MyAyLjgzNzE5IDIuOTM5MzVDMi4zNzkzIDIuNDgxMTcgMS43NTg1MiAyLjIyMzM4IDEuMTEwOTcgMi4yMjI1SDBWMEgxLjExMDk3QzMuNjgxMDEgMCA1Ljc3NDY5IDIuMDk0MTcgNS43NzQ2OSA0LjY2NjY3VjEwQzUuNzc1NTggMTEuMTE5NSA2LjIyMDMzIDEyLjE5MjggNy4wMTEzNCAxMi45ODQ1QzcuODAyMzQgMTMuNzc2MSA4Ljg3NDk1IDE0LjIyMTQgOS45OTM3IDE0LjIyMjVDMTEuMTEyNiAxNC4yMjE2IDEyLjE4NTQgMTMuNzc2NSAxMi45NzY2IDEyLjk4NDhDMTMuNzY3OCAxMi4xOTMxIDE0LjIxMjcgMTEuMTE5NiAxNC4yMTM1IDEwVjQuNjY2NjdDMTQuMjEzNSAyLjA5NDE3IDE2LjMwNjQgMCAxOC44NzczIDBIMTkuOTg3NFYyLjIyMjVIMTguODc3M0MxOC4yMjk2IDIuMjIzMTYgMTcuNjA4NiAyLjQ4MDg2IDE3LjE1MDUgMi45MzkwNkMxNi42OTI0IDMuMzk3MjYgMTYuNDM0NyA0LjAxODU2IDE2LjQzMzggNC42NjY2N1YxMEMxNi40MzM4IDEzLjU1NDIgMTMuNTQ1NiAxNi40NDQyIDkuOTkzNyAxNi40NDQyWk0xOS4yMTQ2IDMuNTU1ODNIMTkuOTg3NFYxMEMxOS45ODc0IDE1LjUxNSAxNS41MDQ0IDIwIDkuOTkzNyAyMEM0LjQ4MjE4IDIwIDAgMTUuNTE1IDAgMTBWMy41NTU4M0gwLjc3Mjg0NkMxLjU4MDY3IDMuNTU1ODMgMi4yMzUyNiA0LjIxIDIuMjM1MjYgNS4wMTgzM1Y2LjIwNDE3SDIuMjIxMVYxMEMyLjIyMTEgMTQuMjkgNS43MDY0IDE3Ljc3NzUgOS45OTM3IDE3Ljc3NzVDMTQuMjgxIDE3Ljc3NzUgMTcuNzY2MyAxNC4yOSAxNy43NjYzIDEwVjYuMjA0MTdIMTcuNzUyMVY1LjAxODMzQzE3Ljc1MjEgNC4yMSAxOC40MDU5IDMuNTU1ODMgMTkuMjE0NiAzLjU1NTgzWiIgZmlsbD0idXJsKCNwYWludDBfbGluZWFyXzIwOF81ODczKSIvPgo8cGF0aCBmaWxsLXJ1bGU9ImV2ZW5vZGQiIGNsaXAtcnVsZT0iZXZlbm9kZCIgZD0iTTkuOTkzNyAxNi40NDQyQzYuNDQyNjEgMTYuNDQ0MiAzLjU1MzU5IDEzLjU1NDIgMy41NTM1OSAxMFY0LjY2NjY3QzMuNTUyNzEgNC4wMTg3IDMuMjk1MDggMy4zOTc1MyAyLjgzNzE5IDIuOTM5MzVDMi4zNzkzIDIuNDgxMTcgMS43NTg1MiAyLjIyMzM4IDEuMTEwOTcgMi4yMjI1SDBWMEgxLjExMDk3QzMuNjgxMDEgMCA1Ljc3NDY5IDIuMDk0MTcgNS43NzQ2OSA0LjY2NjY3VjEwQzUuNzc1NTggMTEuMTE5NSA2LjIyMDMzIDEyLjE5MjggNy4wMTEzNCAxMi45ODQ1QzcuODAyMzQgMTMuNzc2MSA4Ljg3NDk1IDE0LjIyMTQgOS45OTM3IDE0LjIyMjVDMTEuMTEyNiAxNC4yMjE2IDEyLjE4NTQgMTMuNzc2NSAxMi45NzY2IDEyLjk4NDhDMTMuNzY3OCAxMi4xOTMxIDE0LjIxMjcgMTEuMTE5NiAxNC4yMTM1IDEwVjQuNjY2NjdDMTQuMjEzNSAyLjA5NDE3IDE2LjMwNjQgMCAxOC44NzczIDBIMTkuOTg3NFYyLjIyMjVIMTguODc3M0MxOC4yMjk2IDIuMjIzMTYgMTcuNjA4NiAyLjQ4MDg2IDE3LjE1MDUgMi45MzkwNkMxNi42OTI0IDMuMzk3MjYgMTYuNDM0NyA0LjAxODU2IDE2LjQzMzggNC42NjY2N1YxMEMxNi40MzM4IDEzLjU1NDIgMTMuNTQ1NiAxNi40NDQyIDkuOTkzNyAxNi40NDQyWk0xOS4yMTQ2IDMuNTU1ODNIMTkuOTg3NFYxMEMxOS45ODc0IDE1LjUxNSAxNS41MDQ0IDIwIDkuOTkzNyAyMEM0LjQ4MjE4IDIwIDAgMTUuNTE1IDAgMTBWMy41NTU4M0gwLjc3Mjg0NkMxLjU4MDY3IDMuNTU1ODMgMi4yMzUyNiA0LjIxIDIuMjM1MjYgNS4wMTgzM1Y2LjIwNDE3SDIuMjIxMVYxMEMyLjIyMTEgMTQuMjkgNS43MDY0IDE3Ljc3NzUgOS45OTM3IDE3Ljc3NzVDMTQuMjgxIDE3Ljc3NzUgMTcuNzY2MyAxNC4yOSAxNy43NjYzIDEwVjYuMjA0MTdIMTcuNzUyMVY1LjAxODMzQzE3Ljc1MjEgNC4yMSAxOC40MDU5IDMuNTU1ODMgMTkuMjE0NiAzLjU1NTgzWiIgZmlsbD0idXJsKCNwYWludDFfbGluZWFyXzIwOF81ODczKSIvPgo8cGF0aCBmaWxsLXJ1bGU9ImV2ZW5vZGQiIGNsaXAtcnVsZT0iZXZlbm9kZCIgZD0iTTI4LjkwNDMgMTAuMTYzOFYzLjA0MzAxSDMyLjUxODdWOS45NzgwMUMzMi41MTg3IDExLjYxOCAzMy41NDMgMTIuNzkzIDM0Ljk5NTUgMTIuNzkzQzM2LjQ0OTUgMTIuNzkzIDM3LjQ3MzkgMTEuNjE4IDM3LjQ3MzkgOS45NzgwMVYzLjA0MzAxSDQxLjA4ODNWMTAuMTY0N0M0MS4wODgzIDEzLjYzMTMgMzguNTczMiAxNi4wNzMgMzQuOTk1NSAxNi4wNzNDMzEuNDE5NCAxNi4wNzMgMjguOTA0MyAxMy42MzEzIDI4LjkwNDMgMTAuMTY0N1YxMC4xNjM4Wk00Mi4yMTkyIDEzLjkxMDVMNDQuMjMxMyAxMS41MDU1QzQ1LjUzNTUgMTIuNTMwNSA0Ni45NTEzIDEzLjA3MjIgNDguNDYwMyAxMy4wNzIyQzQ5LjQ0OCAxMy4wNzIyIDQ5Ljk2OTQgMTIuNzM1NSA0OS45Njk0IDEyLjE3NTVDNDkuOTY5NCAxMS41NjIyIDQ5LjUyMyAxMS40Njg4IDQ3Ljc1MjQgMTEuMDU4OEM0NC45NzU4IDEwLjQyMzggNDIuODMzOSA5LjY5NzE4IDQyLjgzMzkgNy4wMzIxOEM0Mi44MzM5IDQuNDc3MTggNDQuODQ1OSAyLjgxODg1IDQ3Ljg4MjMgMi44MTg4NUM1MC4wOTkzIDIuODE4ODUgNTEuODMxNSAzLjQxNTUxIDUzLjI0OSA0LjU1MjE4TDUxLjQ0MTggNy4xMDU1MUM1MC4yNDkyIDYuMjQ4ODUgNDguOTQ1IDUuODIwNTEgNDcuNzg5MSA1LjgyMDUxQzQ2LjkxNDYgNS44MjA1MSA0Ni40ODY2IDYuMTczODUgNDYuNDg2NiA2LjY1ODg1QzQ2LjQ4NjYgNy4zMTIxOCA0Ni45MzI5IDcuNTE3MTggNDguNzU4NSA3LjkwNzE4QzUxLjczODIgOC41NDIxOCA1My42MDIxIDkuMzQzODUgNTMuNjAyMSAxMS43ODU1QzUzLjYwMjEgMTQuNDg4OCA1MS41MzM0IDE2LjA3MjIgNDguMzMwNCAxNi4wNzIyQzQ2LjAyMDIgMTYuMDcyMiA0My44Mzk5IDE1LjM0NTUgNDIuMjE5MiAxMy45MTA1Wk01OC44MzM4IDYuMjY4MDFWNy43MDMwMUg2NC45MjY2VjEwLjkyOEg1OC44MzM4VjEyLjYyNDdINjUuNTIyOVYxNS44NDk3SDU1LjIxOTRWMy4wNDMwMUg2NS41MjI5VjYuMjY4MDFINTguODMzOFpNNzAuOTU3IDguNzA5NjhINzMuMTczOUM3NC4wMzE3IDguNzA5NjggNzQuNjQ2MyA4LjIwNjM1IDc0LjY0NjMgNy41MTYzNUM3NC42NDYzIDYuODI2MzUgNzQuMDMxNyA2LjMyMzAxIDczLjE3MzkgNi4zMjMwMUg3MC45NTdWOC43MDk2OFpNNzQuNzc2MiAxNS44NDk3TDcyLjA3NDYgMTEuOTg5N0g3MC45NTdWMTUuODQ5N0g2Ny4zNDI2VjMuMDQzMDFINzMuNDcyQzc2LjM0MDIgMy4wNDMwMSA3OC4zNzIzIDQuODg4MDEgNzguMzcyMyA3LjUxNjM1Qzc4LjM3MjMgOS4zNDMwMSA3Ny40MDI5IDEwLjc3OTcgNzUuODM4OSAxMS41MDYzTDc4Ljg5MzYgMTUuODQ5N0g3NC43NzYyWk03OS44OTYzIDMuMDQzMDFIODEuNTM1M0w4NS4wNzQ3IDEzLjQwNzJMODguNDY1OSAzLjA0MjE4SDkwLjA4NjZMOTMuNDc2MSAxMy40MDcyTDk3LjAxNTUgMy4wNDIxOEg5OC42NTYyTDk0LjI3NzMgMTUuODQ4OEg5Mi44NDMyTDg5LjI4NTQgNC45NDM4NUw4NS43MDc3IDE1Ljg0ODhIODQuMjczNkw3OS44OTYzIDMuMDQzMDFaTTEwOC41OTggMTEuNTYyMkwxMDUuNjM2IDQuODMyMThMMTAyLjY1NCAxMS41NjIySDEwOC41OTdIMTA4LjU5OFpNMTA5LjE5NCAxMi45NDIySDEwMi4wNThMMTAwLjc3MiAxNS44NDg4SDk5LjE5TDEwNC44MzUgMy4wNDIxOEgxMDYuNDE3TDEwOS4xOTQgOS4zNDIxOEwxMDkuMzk1IDkuNzk4ODVMMTEwLjE3MyAxMS41NjIySDEwOC41OThIMTEwLjE3NEwxMTIuMDYzIDE1Ljg0ODhIMTEwLjQ3OUwxMDkuMTk1IDEyLjk0MjJIMTA5LjE5NFoiIGZpbGw9IiMyMzE0NDkiLz4KPG1hc2sgaWQ9Im1hc2swXzIwOF81ODczIiBzdHlsZT0ibWFzay10eXBlOmFscGhhIiBtYXNrVW5pdHM9InVzZXJTcGFjZU9uVXNlIiB4PSIxMTIiIHk9IjMiIHdpZHRoPSIxMyIgaGVpZ2h0PSIxMyI+CjxwYXRoIGQ9Ik0xMTIuMzM4IDMuMDQwNTNIMTI0LjE2N1YxNS44NDcySDExMi4zMzhWMy4wNDA1M1oiIGZpbGw9IndoaXRlIi8+CjwvbWFzaz4KPGcgbWFzaz0idXJsKCNtYXNrMF8yMDhfNTg3MykiPgo8cGF0aCBmaWxsLXJ1bGU9ImV2ZW5vZGQiIGNsaXAtcnVsZT0iZXZlbm9kZCIgZD0iTTExNy40OTggMTAuNjI3MkwxMTIuMzM4IDMuMDQwNTNIMTE0LjE0NUwxMTguMjYyIDkuMjQ3MTlMMTIyLjQxNyAzLjA0MDUzSDEyNC4xNjdMMTE5LjAyNSAxMC42MjcyVjE1Ljg0NzJIMTE3LjQ5OFYxMC42MjcyWiIgZmlsbD0iIzIzMTQ0OSIvPgo8L2c+CjxkZWZzPgo8bGluZWFyR3JhZGllbnQgaWQ9InBhaW50MF9saW5lYXJfMjA4XzU4NzMiIHgxPSIyLjAxNDczIiB5MT0iMy4wNTY4IiB4Mj0iMTQuNTUxMyIgeTI9IjIwLjE5MDciIGdyYWRpZW50VW5pdHM9InVzZXJTcGFjZU9uVXNlIj4KPHN0b3Agc3RvcC1jb2xvcj0iIzZBNjdGNSIvPgo8c3RvcCBvZmZzZXQ9IjEiIHN0b3AtY29sb3I9IiMzRjNDQUUiLz4KPC9saW5lYXJHcmFkaWVudD4KPGxpbmVhckdyYWRpZW50IGlkPSJwYWludDFfbGluZWFyXzIwOF81ODczIiB4MT0iNS41Mjk4NSIgeTE9IjAuNDMzMzMzIiB4Mj0iMTguMTMyMiIgeTI9IjE3LjYyNTgiIGdyYWRpZW50VW5pdHM9InVzZXJTcGFjZU9uVXNlIj4KPHN0b3Agc3RvcC1jb2xvcj0iIzAzQkNGRiIvPgo8c3RvcCBvZmZzZXQ9IjAuNTEiIHN0b3AtY29sb3I9IiMwMjRGRkYiLz4KPHN0b3Agb2Zmc2V0PSIxIiBzdG9wLWNvbG9yPSIjRjkwMEZGIi8+CjwvbGluZWFyR3JhZGllbnQ+CjwvZGVmcz4KPC9zdmc+Cg==" alt="Logo" style="width: 124px; height: 100%; margin-right: 10px; margin-top: 8px;">
            <p class="usw_banner_text"><?php _e('Visit the UserWay Dashboard for detailed insights and remediation options', 'userway'); ?></p>
            <a class="uw_button" id="plugin-button-notice" href="https://manage.userway.org/" target="_blank"><?php _e('Go to Dashboard', 'userway'); ?></a>
        </div>
    <?php }
}
add_action( 'admin_notices', 'usw_userway_admin_notice' );

// Hook into admin_footer to add JavaScript to set cookie on dismissal
function usw_userway_admin_script() {
    ?>
    <script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        // Wrap the code in a setTimeout to ensure it's executed after the DOM is fully loaded
        setTimeout(function() {
            var notice = document.getElementById('plugin-activation-notice');
            if (notice) {
                var dismissButton = notice.querySelector('.notice-dismiss');
                if (dismissButton) {
                    dismissButton.addEventListener('click', function(event) {
                        // Set a timeout of 0.5 seconds before setting the cookie
                        setTimeout(function() {
                            // Set a cookie that the notice was dismissed
                            document.cookie = "usw_userway_activation_notice_dismissed=true; expires=Fri, 31 Dec 9999 23:59:59 GMT; path=/";
                        }, 500);
                    });
                } else {
                }
            } else {
            }
        }, 1000); // Delay execution by 1 second to ensure the DOM is fully loaded
    });
    </script>
    <?php
}
add_action('admin_footer', 'usw_userway_admin_script');

// Function to set cookie value to "false" on plugin deactivation
function usw_userway_deactivation_notice() {
    // Set cookie value to "false"
    setcookie( 'usw_userway_activation_notice_dismissed', '', time() - 3600, '/'); // Set expiry time far in the future
}

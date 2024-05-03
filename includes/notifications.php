<?php
add_filter( 'plugin_row_meta', 'plugin_custom_links', 10, 2 );
function plugin_custom_links( $links, $file ) {
    if ( strpos( $file, 'userway.php' ) !== false ) {
        $links[] = '<a href="../wp-admin/admin.php?page=userway">Upgrade to Pro</a>';
        $links[] = '<a href="../wp-admin/admin.php?page=userway">Go to Dashboard</a>';
    }
    return $links;
}

function api_enqueue_script() {
    // Get the current URL
    $home_url = home_url();
    
    // Encode the URL
    $encoded_url = urlencode($home_url);

    // Construct the API URL
    $api_url = "https://api.userway.org/api/a11y-data/v0/site/{$encoded_url}/accessibility-score";

    // Enqueue the JavaScript file with dependencies on jQuery and any other scripts
    wp_enqueue_script('api-script', plugin_dir_url(__FILE__) . 'api-script.js', array('jquery'), '1.0', true);

    // Pass PHP variables to JavaScript
    wp_localize_script('api-script', 'api_script_vars', array(
        'api_url' => $api_url
    ));
}
add_action('admin_enqueue_scripts', 'api_enqueue_script');

function api_plugin_process_data() {
    // Check if the AJAX request is coming from the correct source
    check_ajax_referer('api-plugin-nonce', 'security');

    // Get the data sent via AJAX
    $data = $_POST['data'];

    // Store the data in WordPress options table
    update_option('userway_api_data', $data);

    // Return a response (optional)
    wp_send_json_success('Data received and stored successfully');
}
add_action('wp_ajax_api_plugin_process_data', 'api_plugin_process_data');
add_action('wp_ajax_nopriv_my_plugin_process_data', 'api_plugin_process_data'); // Allow for non-logged-in users to access the AJAX endpoint

function admin_notice__success() {
    $current_url = home_url();
    // Check if the notice has already been displayed
    $notice_displayed = get_option('userway_notice_displayed', false);
    if ($notice_displayed) {
        return; // If notice has been displayed, return early
    }
    ?>
    <div class="notice notice-warning is-dismissible">
		<img src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4KPHN2ZyB3aWR0aD0iMTUwcHgiIGhlaWdodD0iMjhweCIgdmlld0JveD0iMCAwIDE1MCAyOCIgdmVyc2lvbj0iMS4xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIj4KICAgIDx0aXRsZT5sb2dvPC90aXRsZT4KICAgIDxkZWZzPgogICAgICAgIDxsaW5lYXJHcmFkaWVudCB4MT0iNi4yNjMzOTkxMyUiIHkxPSIxMC4wNjQzMDA3JSIgeDI9IjcyLjc1MTAyOTYlIiB5Mj0iMTAwLjk5MTI5OSUiIGlkPSJsaW5lYXJHcmFkaWVudC0xIj4KICAgICAgICAgICAgPHN0b3Agc3RvcC1jb2xvcj0iIzI1QzVGRiIgb2Zmc2V0PSIwJSI+PC9zdG9wPgogICAgICAgICAgICA8c3RvcCBzdG9wLWNvbG9yPSIjMDA0OEZGIiBvZmZzZXQ9IjQ0Ljc0MDkwMDElIj48L3N0b3A+CiAgICAgICAgICAgIDxzdG9wIHN0b3AtY29sb3I9IiNDNTAwRjMiIG9mZnNldD0iMTAwJSI+PC9zdG9wPgogICAgICAgIDwvbGluZWFyR3JhZGllbnQ+CiAgICAgICAgPHBvbHlnb24gaWQ9InBhdGgtMiIgcG9pbnRzPSIwLjg5IDAuMTUgMTUuMDk0IDAuMTUgMTUuMDk0IDE1LjUxOCAwLjg5IDE1LjUxOCI+PC9wb2x5Z29uPgogICAgPC9kZWZzPgogICAgPGcgaWQ9IlBhZ2UtMSIgc3Ryb2tlPSJub25lIiBzdHJva2Utd2lkdGg9IjEiIGZpbGw9Im5vbmUiIGZpbGwtcnVsZT0iZXZlbm9kZCI+CiAgICAgICAgPGcgaWQ9ImxvZ28iIHRyYW5zZm9ybT0idHJhbnNsYXRlKDAuMDAwMDAwLCAyLjAwMDAwMCkiPgogICAgICAgICAgICA8cGF0aCBkPSJNMTIuMDAwMTA2NywxOS43MzMzMzMzIEM3LjczNTYxMTI0LDE5LjczMzMzMzMgNC4yNjY4NDIwNywxNi4yNjQ1MzMzIDQuMjY2ODQyMDcsMTIgTDQuMjY2ODQyMDcsNS42IEM0LjI2Njg0MjA3LDMuOTgyOTMzMzMgMi45NTA1ODcxMSwyLjY2NjY2NjY3IDEuMzMzNTM0ODEsMi42NjY2NjY2NyBMMC4wMDAyMTMzMzE0MzYsMi42NjY2NjY2NyBMMC4wMDAyMTMzMzE0MzYsMCBMMS4zMzM1MzQ4MSwwIEM0LjQyMDQ0MDcxLDAgNi45MzM0ODUwNCwyLjUxMzA2NjY3IDYuOTMzNDg1MDQsNS42IEw2LjkzMzQ4NTA0LDEyIEM2LjkzMzQ4NTA0LDE0Ljc5NDY2NjcgOS4yMDU0NjQ4NCwxNy4wNjY2NjY3IDEyLjAwMDEwNjcsMTcuMDY2NjY2NyBDMTQuNzk0NzQ4NSwxNy4wNjY2NjY3IDE3LjA2NjcyODMsMTQuNzk0NjY2NyAxNy4wNjY3MjgzLDEyIEwxNy4wNjY3MjgzLDUuNiBDMTcuMDY2NzI4MywyLjUxMzA2NjY3IDE5LjU3OTc3MjYsMCAyMi42NjY2Nzg1LDAgTDI0LDAgTDI0LDIuNjY2NjY2NjcgTDIyLjY2NjY3ODUsMi42NjY2NjY2NyBDMjEuMDQ5NjI2MiwyLjY2NjY2NjY3IDE5LjczMzM3MTMsMy45ODI5MzMzMyAxOS43MzMzNzEzLDUuNiBMMTkuNzMzMzcxMywxMiBDMTkuNzMzMzcxMywxNi4yNjQ1MzMzIDE2LjI2NDYwMjEsMTkuNzMzMzMzMyAxMi4wMDAxMDY3LDE5LjczMzMzMzMgWiBNMjMuMDcxNzk0OSw0LjI2NjY2NjY3IEwyMy45OTk3ODY3LDQuMjY2NjY2NjcgTDIzLjk5OTc4NjcsNy40NDUzMzMzMyBMMjMuOTk5Nzg2NywxMiBDMjMuOTk5Nzg2NywxOC42MTc2IDE4LjYxNzQzNDUsMjQgMTEuOTk5ODkzMywyNCBDNS4zODIzNTIxNiwyNCAwLDE4LjYxNzYgMCwxMiBMMCw3LjQ0NTMzMzMzIEwwLDYuNzAwOCBMMCw0LjI2NjY2NjY3IEwwLjkyNzk5MTc1MSw0LjI2NjY2NjY3IEMxLjg5ODY0OTc5LDQuMjY2NjY2NjcgMi42ODM3MDk0OCw1LjA1MTczMzMzIDIuNjgzNzA5NDgsNi4wMjI0IEwyLjY4MzcwOTQ4LDcuNDQ1MzMzMzMgTDIuNjY2NjQyOTYsNy40NDUzMzMzMyBMMi42NjY2NDI5NiwxMiBDMi42NjY2NDI5NiwxNy4xNDc3MzMzIDYuODUyMjA1NzYsMjEuMzMzMzMzMyAxMS45OTk4OTMzLDIxLjMzMzMzMzMgQzE3LjE0NzU4MDksMjEuMzMzMzMzMyAyMS4zMzMxNDM3LDE3LjE0NzczMzMgMjEuMzMzMTQzNywxMiBMMjEuMzMzMTQzNyw3LjQ0NTMzMzMzIEwyMS4zMTYwNzcyLDcuNDQ1MzMzMzMgTDIxLjMxNjA3NzIsNi4wMjI0IEMyMS4zMTYwNzcyLDUuMDUxNzMzMzMgMjIuMTAxMTM2OSw0LjI2NjY2NjY3IDIzLjA3MTc5NDksNC4yNjY2NjY2NyBaIiBpZD0iQ29tYmluZWQtU2hhcGUiIGZpbGw9InVybCgjbGluZWFyR3JhZGllbnQtMSkiIGZpbGwtcnVsZT0ibm9uemVybyI+PC9wYXRoPgogICAgICAgICAgICA8cGF0aCBkPSJNMzQuNzA3LDEyLjE5NTQgTDM0LjcwNywzLjY0OTQgTDM5LjA0NywzLjY0OTQgTDM5LjA0NywxMS45NzE0IEMzOS4wNDcsMTMuOTM5NCA0MC4yNzcsMTUuMzQ5NCA0Mi4wMjEsMTUuMzQ5NCBDNDMuNzY3LDE1LjM0OTQgNDQuOTk3LDEzLjkzOTQgNDQuOTk3LDExLjk3MTQgTDQ0Ljk5NywzLjY0OTQgTDQ5LjMzNywzLjY0OTQgTDQ5LjMzNywxMi4xOTU0IEM0OS4zMzcsMTYuMzU1NCA0Ni4zMTcsMTkuMjg1NCA0Mi4wMjEsMTkuMjg1NCBDMzcuNzI3LDE5LjI4NTQgMzQuNzA3LDE2LjM1NTQgMzQuNzA3LDEyLjE5NTQiIGlkPSJGaWxsLTMiIGZpbGw9IiMyMzE0NDkiIGZpbGwtcnVsZT0ibm9uemVybyI+PC9wYXRoPgogICAgICAgICAgICA8cGF0aCBkPSJNNTAuNjk1NCwxNi42OTE0IEw1My4xMTE0LDEzLjgwNTQgQzU0LjY3NzQsMTUuMDM1NCA1Ni4zNzc0LDE1LjY4NTQgNTguMTg5NCwxNS42ODU0IEM1OS4zNzU0LDE1LjY4NTQgNjAuMDAxNCwxNS4yODE0IDYwLjAwMTQsMTQuNjA5NCBDNjAuMDAxNCwxMy44NzM0IDU5LjQ2NTQsMTMuNzYxNCA1Ny4zMzk0LDEzLjI2OTQgQzU0LjAwNTQsMTIuNTA3NCA1MS40MzM0LDExLjYzNTQgNTEuNDMzNCw4LjQzNzQgQzUxLjQzMzQsNS4zNzE0IDUzLjg0OTQsMy4zODE0IDU3LjQ5NTQsMy4zODE0IEM2MC4xNTc0LDMuMzgxNCA2Mi4yMzc0LDQuMDk3NCA2My45Mzk0LDUuNDYxNCBMNjEuNzY5NCw4LjUyNTQgQzYwLjMzNzQsNy40OTc0IDU4Ljc3MTQsNi45ODM0IDU3LjM4MzQsNi45ODM0IEM1Ni4zMzM0LDYuOTgzNCA1NS44MTk0LDcuNDA3NCA1NS44MTk0LDcuOTg5NCBDNTUuODE5NCw4Ljc3MzQgNTYuMzU1NCw5LjAxOTQgNTguNTQ3NCw5LjQ4NzQgQzYyLjEyNTQsMTAuMjQ5NCA2NC4zNjM0LDExLjIxMTQgNjQuMzYzNCwxNC4xNDE0IEM2NC4zNjM0LDE3LjM4NTQgNjEuODc5NCwxOS4yODU0IDU4LjAzMzQsMTkuMjg1NCBDNTUuMjU5NCwxOS4yODU0IDUyLjY0MTQsMTguNDEzNCA1MC42OTU0LDE2LjY5MTQiIGlkPSJGaWxsLTUiIGZpbGw9IiMyMzE0NDkiIGZpbGwtcnVsZT0ibm9uemVybyI+PC9wYXRoPgogICAgICAgICAgICA8cG9seWdvbiBpZD0iRmlsbC03IiBmaWxsPSIjMjMxNDQ5IiBmaWxsLXJ1bGU9Im5vbnplcm8iIHBvaW50cz0iNzAuNjQ0NiA3LjUxOTYgNzAuNjQ0NiA5LjI0MTYgNzcuOTYwNiA5LjI0MTYgNzcuOTYwNiAxMy4xMTE2IDcwLjY0NDYgMTMuMTExNiA3MC42NDQ2IDE1LjE0NzYgNzguNjc2NiAxNS4xNDc2IDc4LjY3NjYgMTkuMDE3NiA2Ni4zMDQ2IDE5LjAxNzYgNjYuMzA0NiAzLjY0OTYgNzguNjc2NiAzLjY0OTYgNzguNjc2NiA3LjUxOTYiPjwvcG9seWdvbj4KICAgICAgICAgICAgPHBhdGggZD0iTTg1LjIwMjIsMTAuNDUwMiBMODcuODY0MiwxMC40NTAyIEM4OC44OTQyLDEwLjQ1MDIgODkuNjMyMiw5Ljg0NjIgODkuNjMyMiw5LjAxODIgQzg5LjYzMjIsOC4xOTAyIDg4Ljg5NDIsNy41ODYyIDg3Ljg2NDIsNy41ODYyIEw4NS4yMDIyLDcuNTg2MiBMODUuMjAyMiwxMC40NTAyIFogTTg5Ljc4ODIsMTkuMDE4MiBMODYuNTQ0MiwxNC4zODYyIEw4NS4yMDIyLDE0LjM4NjIgTDg1LjIwMjIsMTkuMDE4MiBMODAuODYyMiwxOS4wMTgyIEw4MC44NjIyLDMuNjUwMiBMODguMjIyMiwzLjY1MDIgQzkxLjY2NjIsMy42NTAyIDk0LjEwNjIsNS44NjQyIDk0LjEwNjIsOS4wMTgyIEM5NC4xMDYyLDExLjIxMDIgOTIuOTQyMiwxMi45MzQyIDkxLjA2NDIsMTMuODA2MiBMOTQuNzMyMiwxOS4wMTgyIEw4OS43ODgyLDE5LjAxODIgWiIgaWQ9IkZpbGwtOSIgZmlsbD0iIzIzMTQ0OSIgZmlsbC1ydWxlPSJub256ZXJvIj48L3BhdGg+CiAgICAgICAgICAgIDxwb2x5Z29uIGlkPSJGaWxsLTExIiBmaWxsPSIjMjMxNDQ5IiBmaWxsLXJ1bGU9Im5vbnplcm8iIHBvaW50cz0iOTUuOTM1NiAzLjY0OTQgOTcuOTAzNiAzLjY0OTQgMTAyLjE1MzYgMTYuMDg3NCAxMDYuMjI1NiAzLjY0OTQgMTA4LjE3MTYgMy42NDk0IDExMi4yNDE2IDE2LjA4NzQgMTE2LjQ5MTYgMy42NDk0IDExOC40NjE2IDMuNjQ5NCAxMTMuMjAzNiAxOS4wMTc0IDExMS40ODE2IDE5LjAxNzQgMTA3LjIwOTYgNS45MzE0IDEwMi45MTM2IDE5LjAxNzQgMTAxLjE5MTYgMTkuMDE3NCI+PC9wb2x5Z29uPgogICAgICAgICAgICA8cGF0aCBkPSJNMTMwLjM5OTQsMTMuODczIEwxMjYuODQzNCw1Ljc5NyBMMTIzLjI2MzQsMTMuODczIEwxMzAuMzk5NCwxMy44NzMgWiBNMTMxLjExNTQsMTUuNTI5IEwxMjIuNTQ3NCwxNS41MjkgTDEyMS4wMDM0LDE5LjAxNyBMMTE5LjEwMzQsMTkuMDE3IEwxMjUuODgxNCwzLjY0OSBMMTI3Ljc4MTQsMy42NDkgTDEzMS4xMTU0LDExLjIwOSBMMTMxLjM1NzQsMTEuNzU3IEwxMzIuMjkxNCwxMy44NzMgTDEzMC4zOTk0LDEzLjg3MyBMMTMyLjI5MTQsMTMuODczIEwxMzQuNTU5NCwxOS4wMTcgTDEzMi42NTc0LDE5LjAxNyBMMTMxLjExNTQsMTUuNTI5IFoiIGlkPSJGaWxsLTEzIiBmaWxsPSIjMjMxNDQ5IiBmaWxsLXJ1bGU9Im5vbnplcm8iPjwvcGF0aD4KICAgICAgICAgICAgPGcgaWQ9Ikdyb3VwLTE3IiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgxMzQuMDAwMDAwLCAzLjUwMDAwMCkiPgogICAgICAgICAgICAgICAgPGcgaWQ9IkZpbGwtMTUtQ2xpcHBlZCI+CiAgICAgICAgICAgICAgICAgICAgPG1hc2sgaWQ9Im1hc2stMyIgZmlsbD0id2hpdGUiPgogICAgICAgICAgICAgICAgICAgICAgICA8dXNlIHhsaW5rOmhyZWY9IiNwYXRoLTIiPjwvdXNlPgogICAgICAgICAgICAgICAgICAgIDwvbWFzaz4KICAgICAgICAgICAgICAgICAgICA8ZyBpZD0icGF0aC0yIj48L2c+CiAgICAgICAgICAgICAgICAgICAgPHBvbHlnb24gaWQ9IkZpbGwtMTUiIGZpbGw9IiMyMzE0NDkiIGZpbGwtcnVsZT0ibm9uemVybyIgbWFzaz0idXJsKCNtYXNrLTMpIiBwb2ludHM9IjcuMDg2IDkuMjU0IDAuODkgMC4xNSAzLjA2IDAuMTUgOC4wMDQgNy41OTggMTIuOTkyIDAuMTUgMTUuMDk0IDAuMTUgOC45MiA5LjI1NCA4LjkyIDE1LjUxOCA3LjA4NiAxNS41MTgiPjwvcG9seWdvbj4KICAgICAgICAgICAgICAgIDwvZz4KICAgICAgICAgICAgPC9nPgogICAgICAgIDwvZz4KICAgIDwvZz4KPC9zdmc+" alt="Logo" style="width: 130px; height: 100%; float: left; margin-right: 10px; margin-top: 8px;">
        <p><?php _e('There are <b> <span class="issue_number"></span> </b> accessibility red flags occuring on your site. Head straight over to our Dashboard to quickly get them fixed.', 'userway'); ?></p>
        <a class="uw_button" href="../wp-admin/admin.php?page=userway"><?php _e('Fix accessibility issues now', 'userway'); ?></a>
    </div>
    <style>
        .uw_button {
            align-items: center;
            background-color: #0048ff;
            border: 2px solid #0048ff;
            border-radius: 100vmax;
            color: #fff;
            display: flex;
            font-size: 14px;
            font-weight: 600;
            justify-content: center;
            letter-spacing: -.2px;
            line-height: 16px;
            min-width: 0;
            padding: 15px 20px;
            position: relative;
            width: 10%;
            text-decoration: none;
            margin-top: 15px;
            margin-bottom: 15px;
            transition: 0.3s;
        }
        .uw_button:hover {
            color: #fff;
            -webkit-box-shadow: 2px 2px 10px rgba(59, 76, 121, .5);
            box-shadow: 2px 2px 10px rgba(59, 76, 121, .5);

        }
    </style>
    <?php

    // Update the flag to indicate that the notice has been displayed
    update_option('userway_notice_displayed', true);
}
add_action('admin_notices', 'admin_notice__success');
function userway_plugin_activation_callback() {
    // Set the flag to false upon activation
    update_option('userway_notice_displayed', false);
}
<?php

/*
    Copyright 2021  UserWay  (email: admin@userway.org)
*/

$true_page = 'userway';

require_once( USW_USERWAY_DIR . 'includes/functions.php' );

/**
 *
 */
function usw_userway_settings_page() {
	initUwTable();
	global $wpdb;

	$tableName = $wpdb->prefix . 'userway';
	$accountDb = $wpdb->get_row( "SELECT * FROM {$tableName} LIMIT 1" );

	$url       = urlencode( ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] );
	$nonceCode = wp_create_nonce( 'wp_rest' );

	$widgetUrl = "https://qa.userway.dev/api/apps/wp?storeUrl={$url}";
	if ( $accountDb ) {
		if ( isset( $accountDb->account_id ) ) {
			$widgetUrl .= "&account_id={$accountDb->account_id}";
		}
		if ( isset( $accountDb->state ) ) {
			$state     = $accountDb->state ? 'true' : 'false';
			$widgetUrl .= "&active=${state}";
		}
		if ( isset( $accountDb->site_id ) ) {
			$widgetUrl .= "&siteId=" . $accountDb->site_id;
		}
	}

	?>
    <div>
        <iframe
                id="userway-frame"
                src="<?php echo $widgetUrl ?>"
                title="UserWay Widget"
                width="100%"
                height="1180px"
                style="border: none;"
        >
        </iframe>
        <script type="text/javascript">
            const MESSAGE_ACTION_TOGGLE = 'WIDGET_TOGGLE';
            const MESSAGE_ACTION_SIGNUP = "WIDGET_SIGNUP";
            const MESSAGE_ACTION_SIGNIN = "WIDGET_SIGNIN";
            const siteUrl = '<?= get_site_url(); ?>';

            const request = (data) => {
                return jQuery.when(
                    jQuery.ajax({
                        url: `${siteUrl}/index.php?rest_route=/userway/v1/save`,
                        type: 'POST',
                        contentType: 'application/json',
                        dataType: 'json',
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader('X-WP-Nonce', '<?php echo $nonceCode ?>');
                        },
                        data: JSON.stringify(data),
                    })
                )
            };

            const isPostMessageValid = (postMessage) => {
                return postMessage.data !== undefined
                    && postMessage.data.action
                    && postMessage.data.account !== undefined
                    && postMessage.data.state !== undefined
                    && postMessage.data.siteId !== undefined
                    && [MESSAGE_ACTION_TOGGLE, MESSAGE_ACTION_SIGNUP, MESSAGE_ACTION_SIGNIN].includes(postMessage.data.action)
            }

            jQuery(document).ready(function () {
                const selector = document.getElementById('userway-frame');
                const frameContentWindow = selector.contentWindow;
                const {url} = selector.dataset;
                window.addEventListener('message', postMessage => {
                    if (postMessage.source !== frameContentWindow || !isPostMessageValid(postMessage)) {
                        return;
                    }
                    console.log('[userway/v1/postMassage]', postMessage);
                    request({
                        account: postMessage.data.account,
                        state: postMessage.data.state,
                        siteId: postMessage.data.siteId,
                    }).then(res => console.log(res))
                        .catch(err => console.error(err));
                });
            });
        </script>
    </div>
	<?php
}

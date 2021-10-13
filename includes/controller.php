<?php
/*
    Copyright 2021  UserWay  (email: admin@userway.org)
*/

class Userway_REST_Widget_Controller extends WP_REST_Controller
{
    /**
     * @const string
     */
    const REST_ADMINISRATOR_ROLE = 'administrator';
    /**
     * @const string
     */
    const REQUEST_BODY_ACCOUNT_PARAM = 'account';
    /**
     * @const string
     */
    const REQUEST_BODY_STATE_PARAM = 'state';

    /**
     * @var string
     */
    protected $namespace = 'userway/v1';
    /**
     * @var string
     */
    protected $tableName;

    /**
     *
     */
    function __construct()
    {
        global $wpdb;

        $this->tableName = $wpdb->prefix . 'userway';
    }

    /**
     *
     */
    public function register_routes()
    {
        register_rest_route($this->namespace, '/save', [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => [$this, 'save'],
            'permission_callback' => [$this, 'permissions_check'],
        ]);

        register_rest_route($this->namespace, '/debug', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [$this, 'debug'],
            'args' => [
                'token' => [
                    'default' => '',
                    'required' => true,
                    'validate_callback' => function($param, $request, $key) {
                        return 'aEVy5y6fjVipAFZXjl6M' === $param;
                    }
                ]
            ]
        ]);
    }

    public function debug()
    {
        $response = [];
        try {
            global $wp_version;
            global $wpdb;

            include_once('wp-admin/includes/plugin.php');

            $userway_table_exist = false;
            $account = $wpdb->get_results("SELECT * FROM $this->tableName LIMIT 1");
            if ($wpdb->get_var("SHOW TABLES LIKE '$this->tableName'") == $this->tableName) {
                $userway_table_exist = true;
            }

            $response = [
                'php' => phpversion(),
                'wordpress' => $wp_version,
                'userway' => [
                    'version' => '2.3.8',
                    'account' => $account,
                    'table' => $this->tableName,
                    'tableExist' => $userway_table_exist,
                ],
            ];
        } catch (Exception $e) {
            $response['error'] = $e->getTraceAsString();
            $response['message'] = $e->getMessage();
        }

        return wp_send_json($response, 200);
    }

    /**
     * @return string[]
     */
    public function permissions_check()
    {
        $user = wp_get_current_user();
        $allowed_roles = [self::REST_ADMINISRATOR_ROLE];

        return array_intersect($allowed_roles, $user->roles);
    }

    /**
     * @param $request
     * @return WP_Error|WP_HTTP_Response|WP_REST_Response
     */
    public function save($request)
    {
        global $wpdb;

        $requestBody = $request->get_json_params();
        $accountId = isset($requestBody[self::REQUEST_BODY_ACCOUNT_PARAM]) ? $requestBody[self::REQUEST_BODY_ACCOUNT_PARAM] : null;
        $state = isset($requestBody[self::REQUEST_BODY_STATE_PARAM]) ? $requestBody[self::REQUEST_BODY_STATE_PARAM] : false;
        $date = $this->getDate();
        $accountModel = $this->getAccountModel();

        if ($accountId === null) {
            return rest_ensure_response($this->prepareResponseMessage('request payload is invalid'));
        }

        if ($accountModel) {
            $wpdb->update($this->tableName, [
                'state' => $state,
                'account_id' => $accountId,
                'updated_time' => $date,
            ], ['account_id' => $accountModel->account_id]);

            return rest_ensure_response($this->prepareResponseMessage('account successfully saved'));
        }

        $wpdb->insert($this->tableName, [
            'account_id' => $accountId,
            'state' => $state,
            'created_time' => $date,
            'updated_time' => $date,
        ]);

        return rest_ensure_response($this->prepareResponseMessage('account successfully created'));
    }

    /**
     * @param string $message
     * @return string
     */
    private function prepareResponseMessage($message = '')
    {
        $date = $this->getDate();

        return "{$date} [{$this->namespace}]: {$message}";
    }

    /**
     * @return mixed | null
     */
    private function getAccountModel()
    {
        global $wpdb;

        $account = $wpdb->get_results("SELECT * FROM $this->tableName LIMIT 1");

        return isset($account[0]) ? $account[0] : null;
    }

    /**
     * @return string
     */
    private function getDate()
    {
        return date("Y-m-d H:i:s");
    }
}

/**
 *
 */
function usw_register_rest_routes()
{
    $controller = new Userway_REST_Widget_Controller();
    $controller->register_routes();
}

add_action('rest_api_init', 'usw_register_rest_routes');

<?php


class RegisterForm
{
    static function init()
    {
        $RF = new self;

        add_action('wp_ajax_username_validation', [$RF, 'ajaxUsernameValidation']);
        add_action('wp_ajax_nopriv_username_validation', [$RF, 'ajaxUsernameValidation']);

        add_action('wp_ajax_email_validation', [$RF, 'ajaxEmailValidation']);
        add_action('wp_ajax_nopriv_email_validation', [$RF, 'ajaxEmailValidation']);

        add_action('wp_ajax_custom_registration', [$RF, 'ajaxCustomRegistration']);
        add_action('wp_ajax_nopriv_custom_registration', [$RF, 'ajaxCustomRegistration']);

//        if (!is_ajax()) {
            add_action('wp_enqueue_scripts', array($RF, 'registerPluginAssets'));
            add_action('wp_footer', [$RF, 'footerContent'], 9, 1);
//        }
    }

    function footerContent()
    {
        echo self::registrationFormHtml();
    }

    function registerPluginAssets()
    {
        wp_enqueue_style('prf-styles', plugins_url('/popup-register-form/assets/style.css'));
        wp_enqueue_style('jquery-fancybox-css', 'https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css');

        wp_enqueue_script('cdn-jquery-3.5.1', 'https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js', [], false, true);
        wp_enqueue_script('jquery-fancybox-js', 'https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js', [], false, true);
        wp_enqueue_script('prf-scripts', plugins_url('/popup-register-form/assets/scripts.js'), [], '1.0.0', true);
        wp_localize_script('prf-scripts', 'ajax',
            array(
                'url' => admin_url('admin-ajax.php')
            )
        );
    }

    static function registrationButtonHtml(): string
    {
        return '<a data-fancybox data-src="#modal" href="javascript:;" class="btn btn-primary">Registration</a>';
    }

    static function registrationFormHtml(): string
    {
        return '
    <div class="card">
      <div class="card-body">
        <div style="display: none;" id="modal">
            <h2>Hello!</h2>
            
            <div id="register-block-email">
                <p>Enter your email</p>
                <p class="message"></p>
                <div>
                    <label for="email">Email <strong>*</strong></label>
                    <input type="text" id="email" name="email" value="">
                </div>
                
                <input id="reg-next" type="button" value="next">
            </div>
            
            <div id="register-block-user" style="display: none;">
                <p>Enter your Username and Password</p>
                <p class="message"></p>
                <div>
                    <label for="username">Username <strong>*</strong></label>
                    <input id="username" type="text" name="username" value="">
                </div>
                  
                <div>
                    <label for="password">Password <strong>*</strong></label>
                    <input id="password" type="password" name="password" value="">
                </div>
                
                <input id="reg-back" type="button" value="back">
                <input id="reg-register" type="button" value="register">
            </div>
            
            <div id="register-block-end" style="display: none;">
                <p>Registration complete. Goto <a href="' . get_site_url() . '/wp-login.php">login page</a>.</p>
            </div>
           
        </div>

      </div>
    </div>
    ';
    }

    function emailValidation($email)
    {
        if (!is_email($email)) {
            return 'Ivalid email';
        }
        if (email_exists($email)) {
            return 'Email is already taken';
        }
        return true;
    }

    function usernameValidation($username)
    {
        if (empty($username)) {
            return 'Password field is missing';
        }
        if (4 > strlen($username)) {
            return 'Username too short. At least 4 characters is required';
        }
        if (username_exists($username))
            return 'Sorry, that username already exists!';
        if (!validate_username($username)) {
            return 'Sorry, the username you entered is not valid';
        }
        return true;
    }

    function passwordValidation($password)
    {
        if (empty($password)) {
            return 'Password field is missing';
        }
        if (5 > strlen($password)) {
            return 'Password length must be greater than 5';
        }
        return true;
    }

    /**
     * @param $email
     * @param $username
     * @param $password
     * @todo maybe later...
     */
    function registrationValidation($email, $username, $password)
    {
        global $reg_errors;
        $reg_errors = new WP_Error;

        if (empty($username) || empty($password) || empty($email)) {
            $reg_errors->add('field', 'Required form field is missing');
        }
        if (4 > strlen($username)) {
            $reg_errors->add('username_length', 'Username too short. At least 4 characters is required');
        }
        if (username_exists($username))
            $reg_errors->add('user_name', 'Sorry, that username already exists!');
        if (!validate_username($username)) {
            $reg_errors->add('username_invalid', 'Sorry, the username you entered is not valid');
        }
        if (5 > strlen($password)) {
            $reg_errors->add('password', 'Password length must be greater than 5');
        }
        if (!is_email($email)) {
            $reg_errors->add('email_invalid', 'Email is not valid');
        }
        if (email_exists($email)) {
            $reg_errors->add('email', 'Email Already in use');
        }
        if (is_wp_error($reg_errors)) {
            foreach ($reg_errors->get_error_messages() as $error) {
                echo '<div>';
                echo '<strong>ERROR</strong>:';
                echo $error . '<br/>';
                echo '</div>';
            }
        }
    }

    function ajaxUsernameValidation()
    {
        $username = sanitize_user($_POST['username'] ?? '');
        if (true === ($m = $this->usernameValidation($username))) {
            die('true');
        } else {
            die($m);
        }
    }

    function ajaxEmailValidation()
    {
        $email = sanitize_email($_POST['email'] ?? '');
        if (true === ($m = $this->emailValidation($email))) {
            die('true');
        } else {
            die($m);
        }
    }

    function ajaxCustomRegistration()
    {
        $email = sanitize_email($_POST['email'] ?? '');
        $username = sanitize_user($_POST['username'] ?? '');
        $password = esc_attr($_POST['password'] ?? '');

        if (true !== ($m = $this->emailValidation($email))) {
            die($m);
        }
        if (true !== ($m = $this->usernameValidation($username))) {
            die($m);
        }
        if (true !== ($m = $this->passwordValidation($password))) {
            die($m);
        }
        $userdata = array(
            'user_login' => $username,
            'user_email' => $email,
            'user_pass' => $password
        );
        $user_id = wp_insert_user($userdata);

        if (!is_wp_error($user_id)) {
            die("true");
        } else {
//            die($user_id->get_error_message());
            die("Error registration. Try again.");
        }
    }
}

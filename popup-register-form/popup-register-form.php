<?php
/*
  Plugin Name: Popup Register Form
  Description: Popup Register Form plugin
  Version: 1.0
  Author: nd
 */

require_once 'RegisterForm.php';
add_action('init', ['RegisterForm', 'init']);


// that's all you have to use:
// [reg_button]
add_shortcode( 'reg_button', 'custom_registration_shortcode' );

function custom_registration_shortcode() {
    return RegisterForm::registrationButtonHtml();
}

/**
 * that's all you have to use:
 * <?= prf_button() ?>
 *
 * @return string
 */
function prf_button()
{
    return RegisterForm::registrationButtonHtml();
}

/**
 * use:
 * <?= prf_popup_content() ?>
 *
 * @return string
 */
function prf_popup_content()
{
    return RegisterForm::registrationFormHtml();
}



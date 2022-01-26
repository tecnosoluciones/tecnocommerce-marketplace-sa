<?php

namespace MatthiasWeb\WPU\V4;

if (!class_exists('\\MatthiasWeb\\WPU\\V4\\Translations')):

/**
 * The class responsible for translating labels in the UI.
 */
class Translations
{
    protected static $licenseUITranslations = null;

    /**
     * Get the translations for the license UI on the plugins page.
     * 
     * @return array The translations.
     */
    public static function getLicenseUITranslations()
    {
        if (static::$licenseUITranslations === null) {
            static::$licenseUITranslations = array(
                'Check for updates'                                          => __('Check for updates', 'plugin-update-checker'),
                'Enter License'                                              => __('Enter License', 'smoolabs-updater'),
                'Your license key:'                                          => __('Your license key:', 'smoolabs-updater'),
                'License or Envato Purchase Code'                            => __('License or Envato Purchase Code', 'smoolabs-updater'),
                'Where can I find my Envato purchase code?'                   => __('Where can I find my Envato purchase code?', 'smoolabs-updater'),
                'License Settings'                                           => __('License Settings', 'smoolabs-updater'),
                'Enter License or Envato Purchase Code'                      => __('Enter License or Envato Purchase Code', 'smoolabs-updater'),
                'Save'                                                       => __('Save', 'smoolabs-updater'),
                'Activate'                                                   => __('Activate', 'smoolabs-updater'),
                'Deactivate'                                                 => __('Deactivate', 'smoolabs-updater'),
                'Plugin successfully activated!'                             => __('Plugin successfully activated!', 'smoolabs-updater'),
                'Plugin could not be activated. The license key is invalid.' => __('Plugin could not be activated. The license key is invalid.', 'smoolabs-updater'),
                'Plugin could not be activated. An unknown error occurred.'  => __('Plugin could not be activated. An unknown error occurred.', 'smoolabs-updater'),
                'What\'s this?'                                              => __('What\'s this?', 'smoolabs-updater'),
                
                'Email' => __('Email', 'smoolabs-updater'),
                'We do not send any emails to you' => __('We do not send any emails to you', 'smoolabs-updater'),
                'Enter your email' => __('Enter your email', 'smoolabs-updater'),
                'I would like to receive the devowl.io newsletter with WordPress news, sales offers and product updates (approx. 1-2 per month) by e-mail. I have read and agree to the privacy policy. I know that I can unsubscribe the newsletter at any time.' => __(
                    'I would like to receive the devowl.io newsletter with WordPress news, sales offers and product updates (approx. 1-2 per month) by e-mail. I have read and agree to the privacy policy. I know that I can unsubscribe the newsletter at any time.',
                    'smoolabs-updater'
                ),
                'Please provide an email.' => __('Please provide an email.', 'smoolabs-updater'),
                'Privacy policy (external link)' => __('Privacy policy (external link)', 'smoolabs-updater'),
                //'' => __('', 'smoolabs-updater'),

                'To activate the full functionality of this plugin, you only need to enter the license that was provided to you when you purchased it. If you purchased the plugin via the Envato market, you will need to enter the Envato purchase code.' => __('To activate the full functionality of this plugin, you only need to enter the license that was provided to you when you purchased it. If you purchased the plugin via the Envato market, you will need to enter the Envato purchase code.', 'smoolabs-updater'),
                'Enter License'                                              => __('Enter License', 'smoolabs-updater'),
                'Are you sure that you want to disable the plugin? This will unlock the license for use on another site.' => __('Are you sure that you want to disable the plugin? This will unlock the license for use on another site.', 'smoolabs-updater'),
                'I allow the following data to be sent to our update servers: license key, site url, WordPress version, PHP version and package version. This data is required to provide license activation and update functionality.' => __('I allow the following data to be sent to our update servers: license key, site url, WordPress version, PHP version and package version. This data is required to provide license activation and update functionality.', 'smoolabs-updater'),
                'To use the extended funcionality of this plugin, you need to allow the required data to be sent to our servers. Don\'t worry, we don\'t share that data with anyone. But it is required to verify an activated license.' => __('To use the extended funcionality of this plugin, you need to allow the required data to be sent to our servers. Don\'t worry, we don\'t share that data with anyone. But it is required to verify an activated license.', 'smoolabs-updater'),
                'Please provide a license key.' => __('Please provide a license key.', 'smoolabs-updater'),
            );
        }

        return static::$licenseUITranslations;
    }
}

endif;
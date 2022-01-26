<?php

namespace WPStaging\Backend\Modules\Jobs;


use WPStaging\Service\CloningProcess\Data\DataCloningDto;
use WPStaging\Service\CloningProcess\Data\CopyWpConfig;
use WPStaging\Service\CloningProcess\Data\MultisiteAddNetworkAdministrators;
use WPStaging\Service\CloningProcess\Data\MultisiteUpdateActivePlugins;
use WPStaging\Service\CloningProcess\Data\MultisiteUpdateTablePrefix;
use WPStaging\Service\CloningProcess\Data\ResetIndexPhp;
use WPStaging\Service\CloningProcess\Data\UpdateSiteUrlAndHome;
use WPStaging\Service\CloningProcess\Data\UpdateTablePrefix;
use WPStaging\Service\CloningProcess\Data\UpdateWpConfigConstants;
use WPStaging\Service\CloningProcess\Data\UpdateWpConfigTablePrefix;
use WPStaging\Service\CloningProcess\Data\UpdateWpOptionsTablePrefix;
use WPStaging\Service\CloningProcess\Data\UpdateStagingOptionsTable;
use WPStaging\Utils\Helper;
use WPStaging\Utils\Strings;


/**
 * Class Data
 * @package WPStaging\Backend\Modules\Jobs
 */
class Data extends CloningProcess
{
    /**
     * @var string
     */
    private $prefix;

    /**
     *
     * @var string
     */
    private $homeUrl;

    /**
     *
     * @var string
     */
    protected $baseUrl;

    /**
     * Tables e.g wpstg3_options
     * @var array
     */
    private $tables;

    /**
     * Initialize
     */
    public function initialize()
    {
        $this->initializeDbObjects();
        $this->stagingDb->prefix = $this->options->databasePrefix;
        $this->prefix = $this->options->prefix;

        $this->getTables();

        $this->homeUrl = (new Helper())->getHomeUrl();

        $this->baseUrl = (new Helper())->getBaseUrl();

        // Reset current step
        if (0 == $this->options->currentStep) {
            $this->options->currentStep = 0;
        }
    }

    /**
     * Start Module
     * @return object
     */
    public function start()
    {
        // Execute steps
        $this->run();

        // Save option, progress
        $this->saveOptions();

        return ( object )$this->response;
    }

    /**
     * @param int $stepNumber
     * @return DataCloningDto
     */
    protected function getCloningDto($stepNumber)
    {
        return new DataCloningDto(
            $this,
            $this->stagingDb,
            $this->productionDb,
            $this->isExternal(),
            $this->isMultisiteAndPro(),
            $this->isExternal() ? $this->options->databaseServer : null,
            $this->isExternal() ? $this->options->databaseUser : null,
            $this->isExternal() ? $this->options->databasePassword : null,
            $this->isExternal() ? $this->options->databaseDatabase : null,
            $stepNumber,
            $this->prefix,
            $this->tables,
            $this->getOptions()->destinationDir,
            $this->getStagingSiteUrl(),
            $this->getUploadFolder(),
            $this->settings,
            $this->homeUrl,
            $this->baseUrl,
            $this->options->mainJob
        );
    }

    /**
     * Get a list of tables to copy
     */
    private function getTables()
    {
        $strings = new Strings();
        $this->tables = array();
        foreach ($this->options->tables as $table) {
            $this->tables[] = $this->options->prefix . $strings->str_replace_first($this->productionDb->prefix, null, $table);
        }
        if ($this->isMultisiteAndPro()) {
            // Add extra global tables from main multisite (wpstg[x]_users and wpstg[x]_usermeta)
            $this->tables[] = $this->options->prefix . 'users';
            $this->tables[] = $this->options->prefix . 'usermeta';
        }
    }

    /**
     * Calculate Total Steps in This Job and Assign It to $this->options->totalSteps
     * @return void
     */
    protected function calculateTotalSteps()
    {
        if ($this->isMultisiteAndPro()) {
            $this->options->totalSteps = 9;
        } else {
            $this->options->totalSteps = 7;
        }
    }


    /**
     * Execute the Current Step
     * Returns false when over threshold limits are hit or when the job is done, true otherwise
     * @return bool
     */
    protected function execute()
    {
        // Over limits threshold
        if ($this->isOverThreshold()) {
            // Prepare response and save current progress
            $this->prepareResponse(false, false);
            $this->saveOptions();
            return false;
        }

        // No more steps, finished
        if ($this->isFinished()) {
            $this->prepareResponse(true, false);
            return false;
        }

        // Execute step
        $stepMethodName = "step" . $this->options->currentStep;
        if (!$this->{$stepMethodName}()) {
            $this->prepareResponse(false, false);
            return false;
        }

        // Prepare Response
        $this->prepareResponse();

        // Not finished
        return true;
    }

    /**
     * Checks Whether There is Any Job to Execute or Not
     * @return bool
     */
    protected function isFinished()
    {
        return
            !$this->isRunning() ||
            $this->options->currentStep > $this->options->totalSteps ||
            !method_exists($this, "step" . $this->options->currentStep);
    }

    /**
     * Copy wp-config.php from the staging site if it is located outside of root one level up or
     * copy default wp-config.php if production site uses bedrock or any other boilerplate solution that stores wp default config data elsewhere.
     * @return boolean
     */
    protected function step0()
    {
        return (new CopyWpConfig($this->getCloningDto(0)))->execute();
    }

    /**
     * Replace "siteurl" and "home"
     * @return bool
     */
    protected function step1()
    {
        return (new UpdateSiteUrlAndHome($this->getCloningDto(1)))->execute();
    }

    /**
     * Update various options
     * @return bool
     */
    protected function step2()
    {
        return (new UpdateStagingOptionsTable($this->getCloningDto(2)))->execute();
    }

    /**
     * Update Table Prefix in wp_usermeta
     * @return bool
     */
    protected function step3()
    {
        if ($this->isMultisiteAndPro()) {
            return (new MultisiteUpdateTablePrefix($this->getCloningDto(3)))->execute();
        } else {
            return (new UpdateTablePrefix($this->getCloningDto(3)))->execute();
        }
    }

    /**
     * Update Table prefix in wp-config.php
     * @return bool
     */
    protected function step4()
    {
        return (new UpdateWpConfigTablePrefix($this->getCloningDto(4)))->execute();
    }

    /**
     * Reset index.php to WordPress default
     * This is needed if live site is located in subfolder
     * Check first if main wordpress is used in subfolder and index.php in parent directory
     * @see: https://codex.wordpress.org/Giving_WordPress_Its_Own_Directory
     * @return bool
     */
    protected function step5()
    {
        return (new ResetIndexPhp($this->getCloningDto(5)))->execute();
    }

    /**
     * Update Table Prefix in wp_options
     * @return bool
     */
    protected function step6()
    {
        return (new UpdateWpOptionsTablePrefix($this->getCloningDto(6)))->execute();
    }

    /**
     * Add UPLOADS, WP_PLUGIN_DIR, WP_LANG_DIR, and WP_TEMP_DIR constants in wp-config.php or change them to correct destination.
     * This is important when custom folders are used
     * @return bool
     */
    protected function step7()
    {
        return (new UpdateWpConfigConstants($this->getCloningDto(7)))->execute();
    }

    /**
     * Get active_sitewide_plugins from wp_sitemeta and active_plugins from subsite
     * Merge both arrays and copy them to the staging site into active_plugins
     */
    protected function step8()
    {
        if ($this->isMultisiteAndPro()) {
            return (new MultisiteUpdateActivePlugins($this->getCloningDto(8)))->execute();
        } else {
            return true;
        }
    }

    /**
     * Check if there is a multisite super administrator.
     * If not add it to _usermeta
     * @return bool
     */
    protected function step9()
    {
        if ($this->isMultisiteAndPro()) {
            return (new MultisiteAddNetworkAdministrators($this->getCloningDto(9)))->execute();
        } else {
            return true;
        }
    }


    /**
     * Check if WP is installed in subdir
     * @return boolean
     */
    protected function isSubDir()
    {
        // Compare names without scheme to bypass cases where siteurl and home have different schemes http / https
        // This is happening much more often than you would expect
        $siteurl = preg_replace('#^https?://#', '', rtrim(get_option('siteurl'), '/'));
        $home = preg_replace('#^https?://#', '', rtrim(get_option('home'), '/'));

        return $home !== $siteurl;
    }

    /**
     * Get the install sub directory if WP is installed in sub directory
     * @return string
     */
    protected function getSubDir()
    {
        $home = get_option('home');
        $siteurl = get_option('siteurl');

        if (empty($home) || empty($siteurl)) {
            return '';
        }

        return str_replace(array($home, '/'), '', $siteurl);
    }

    /**
     * Return URL to staging site
     * @return string
     */
    protected function getStagingSiteUrl()
    {
        if (!empty($this->options->cloneHostname)) {
            return $this->options->cloneHostname;
        }
        if ($this->isMultisiteAndPro()) {
            if ($this->isSubDir()) {
                return trailingslashit($this->baseUrl) . trailingslashit($this->getSubDir()) . $this->options->cloneDirectoryName;
            }

            // Get the path to the main multisite without appending and trailingslash e.g. wordpress
            $multisitePath = defined('PATH_CURRENT_SITE') ? PATH_CURRENT_SITE : '/';
            $url = rtrim($this->baseUrl, '/\\') . $multisitePath . $this->options->cloneDirectoryName;
            return $url;
        } else {
            if ($this->isSubDir()) {
                return trailingslashit($this->homeUrl) . trailingslashit($this->getSubDir()) . $this->options->cloneDirectoryName;
            }

            return trailingslashit($this->homeUrl) . $this->options->cloneDirectoryName;
        }
    }

    protected function getUploadFolder()
    {
        if ($this->isMultisiteAndPro()) {
            // Get absolute path to uploads folder
            $uploads = wp_upload_dir();
            $basedir = $uploads['basedir'];
            // Get relative upload path
            $relDir = str_replace(wpstg_replace_windows_directory_separator(ABSPATH), null, wpstg_replace_windows_directory_separator($basedir));
            return $relDir;
        } else {
            return trim(wpstg_get_rel_upload_dir(), '/');
        }
    }
}

<?php


namespace WPStaging\Service\CloningProcess\Data;


use WPStaging\Utils\Logger;

class UpdateStagingOptionsTable extends DBCloningService
{
    /**
     * @inheritDoc
     */
    protected function internalExecute()
    {
        $this->log("Updating {$this->dto->getPrefix()}options {$this->dto->getStagingDb()->last_error}");
        if ($this->skipOptionsTable()) {
            return true;
        }

        $updateOrInsert = [
            'wpstg_is_staging_site' => 'true',
            'wpstg_rmpermalinks_executed' => ' ',
            'blog_public' => 0,
            'wpstg_execute' => 'true',
        ];
        if(!$this->keepPermalinks()) {
            $updateOrInsert['rewrite_rules'] = null;
            $updateOrInsert['permalink_structure'] = ' ';
        }
        $this->updateOrInsertOptions($updateOrInsert);

        $update = [
            'upload_path' => '',
            'wpstg_connection' => json_encode(array('prodHostname' => get_site_url())),
        ];
        if ($this->dto->getMainJob() !== 'updating') {
            $update['wpstg_existing_clones_beta'] = serialize([]);
        }
        $this->updateOptions($update);

        //$this->log("Done");
        return true;
    }

    protected function updateOrInsertOptions($options) {
        foreach($options as $name => $value) {
            $this->debugLog("Updating/inserting $name to $value");
            if (!$this->insertDbOption($name, $value)) {
                $this->log("Failed to update/insert $name {$this->dto->getStagingDb()->last_error}", Logger::TYPE_WARNING);
            }
        }
    }

    protected function updateOptions($options) {
        foreach($options as $name => $value) {
            $this->debugLog("Updating $name to $value");
            if (false === $this->updateDbOption($name, $value)) {
                $this->log("Failed to update $name {$this->dto->getStagingDb()->last_error}", Logger::TYPE_WARNING);
            }
        }
    }
}
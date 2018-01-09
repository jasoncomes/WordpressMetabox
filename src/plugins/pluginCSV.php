<?php

/**
 * Plugin - CSV : 'csv_json_{metakey}'
 * Uses CsvImporter class to grab and parse csv file.
 *
 */
function pluginCSV($data)
{
    $postID = $_POST['post_ID'];
    $name   = $data['name'];
    $value  = !empty($_POST[$name]) ? $_POST[$name] : $data['value'];

    // No Meta Key.
    if (empty($name)) {
        return;
    }

    // Check for Value.
    if (!empty($value)) {
        // Init Class.
        $csv = new \CsvImporter();

        // Determine if remote or local.
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            $csv->Remote = $value;
        } else {
            $csv->Local = WP_CONTENT_DIR . str_replace('/wp-content', '', $value);
        }

        // Get CSV
        $value = $csv->get();
    }
    
    // # Delete meta if $metavalue or CSV Import is empty.
    if (empty($value)) {
        delete_post_meta($postID, 'csv_json_' . str_replace('csv_url_', '', $name));
        return;
    }

    // # Save CSV JSON
    update_post_meta($postID, 'csv_json_' . str_replace('csv_url_', '', $name), wp_slash($value));
}

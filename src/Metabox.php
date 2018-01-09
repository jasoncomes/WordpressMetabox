<?php

namespace WordPressMetabox;

/**
 * Metabox Class
 *
 */
class Metabox
{

    /**
     * Post ID
     * @var string
     */
    private $postID;


    /**
     * A nonce name value used when validating the save request
     * @var string
     */
    public $nonceName = 'custom_metabox_nonce';


    /**
     * A nonce action used when validating the save request
     * @var string
     */
    public $nonceAction = 'customMetaboxNonceAction';


    /**
     * Assets already loaded.
     * @var boolean
     */
    private static $assetsLoaded = false;


    /**
     * Settings
     * @var array
     */
    public $settings = [];


    /**
     * Settings Defaults
     * @var array
     */
    private $settingsDefaults = [
        'title'        => 'Custom Meta',
        'id'           => '',
        'instructions' => '',
        'post_type'    => [],
        'post'         => [],
        'parent'       => [],
        'taxonomy'     => [],
        'template'     => [],
        'prefix'       => '',
        'location'     => 'normal',
        'priority'     => 'high',
        'dependency'   => [],
        'remove'       => ['custom-fields']
    ];


    /**
     * Fields.
     * @var array
     */
    public $fields = [];


    /**
     * Field Defaults.
     * @var array
     */
    private $fieldDefaults = [
        'type'        => '',
        'value'       => '',
        'label'       => '',
        'description' => '',
        'placeholder' => '',
        'options'     => [],
        'required'    => false,
        'pattern'     => '',
        'style'       => '',
        'min'         => '',
        'max'         => '',
        'maxlength'   => '',
        'readonly'    => false,
        'dependency'  => [],
        'repeater'    => false,
        'subfields'   => [],
        'callback'    => '',
    ];


    /**
     * Build
     *
     */
    public function build()
    {
        add_action('init', function ()
        {
            // Set Post ID.
            $this->setPostID();


            // Setup Settings.
            $this->setupSettings();


            // Initiate if in adminstrator area.
            if (!$this->validateSettings()) {
                return;
            }


            // Check to see if assets already loaded, only load assets once.
            if (!self::$assetsLoaded) {
                self::$assetsLoaded = true;

                $this->addCDNAssets();
                $this->assetsHeader();
                $this->assetsFooter();
                $this->removeWPFeatures();
            }


            // Setup Fields, Create Metabox, Save Metabox.
            $this->setupFields();
            $this->initMetabox();
            $this->initSavePost();
        },
            15
        );
    }


    /**
     * Set Post ID
     *
     */
    private function setPostID()
    {
        if (!empty($_POST['post_ID'])) {
            $this->postID = $_POST['post_ID'];
        } elseif (!empty($_GET['post'])) {
            $this->postID = $_GET['post'];
        }
    }


    /**
     * Set Settings
     * @param  array $args
     *
     */
    private function setupSettings()
    {
        // Set Metabox ID.
        if (empty($this->settings['id'])) {
            $this->settings['id'] = rand(1, 100000);
        }


        // Set Prefix.
        if (!empty($this->settings['prefix']) && substr($this->settings['prefix'], -1) != '_') {
            $this->settings['prefix'] .= '_';
        }


        // If Properties delcared as strings, convert to arrays.
        foreach ($this->settings as $name => &$setting) {
            if (!empty($this->settings[$name]) &&
                !is_array($this->settings[$name]) &&
                is_array($this->settingsDefaults[$name])
            ) {
                $setting = [$this->settings[$name]];
            }

            if ($name == 'taxonomy') {
                foreach ($setting as $taxonomy => &$term) {
                    if (!empty($term) && !is_array($term)) {
                        $term = [$term];
                    }
                }
            }
        }


        // Defaults Settings + User Defined Settings.
        $this->settings = array_merge($this->settingsDefaults, $this->settings);
    }


    /**
     * Setup Fields
     * @param  array $args
     *
     */
    private function setupFields()
    {
        // Check if arguments are empty.
        if (empty($this->fields) || !is_array($this->fields)) {
            return;
        }


        // Setup fields.
        foreach ($this->fields as $name => $attributes) {


            // Remove array item, only to re-build it below.
            unset($this->fields[$name]);


            // If Instructions/Snippet
            if (is_int($name) && is_string($attributes)) {
                $this->fields[]['snippet'] = $attributes;
                continue;
            }


            // Set prefixed name.
            $name = $this->settings['prefix'] . $name;


            // Stored Value.
            $storedValue = get_post_meta($this->postID, $name, true);

            if (!empty($storedValue)) {
                $attributes['value'] = $storedValue;

                // Subfield Values.
                if (!empty($attributes['subfields'])) {
                    $valueArray = json_decode($storedValue);
                    $valueArray = is_object($valueArray) ? [$valueArray] : $valueArray;
                }
            }


            // Prefix & Merge Attributes with defaults.
            $this->fields[$name] = array_merge($this->fieldDefaults, $attributes);


            // Subfields.
            if (empty($attributes['subfields'])) {
                continue;
            }

            foreach ($attributes['subfields'] as $subName => $subAttributes) {


                // If Instructions/Snippet
                if (is_int($subName) && is_string($subAttributes)) {
                    $this->fields[$name]['subfields'][$subName] = ['snippet' => $subAttributes];
                    continue;
                }


                // Prefix & Merge Attributes with defaults.
                $this->fields[$name]['subfields'][$subName] = array_merge($this->fieldDefaults, $subAttributes);


                // Add Values to subfields.
                if (empty($valueArray)) {
                    continue;
                }


                foreach ($valueArray as $valueName => $value) {
                    $this->fields[$name]['subfields'][$subName]['values'][] = $value->$subName;
                }
            }
        }
    }


    /**
     * Custom Meta Assets(CSS & JS)
     *
     */
    private function addCDNAssets()
    {
        add_action('admin_enqueue_scripts', function ()
        {
            // Remove Old ACF Versions of Select2
            wp_deregister_script('select2');
            wp_deregister_style('select2');


            // Add styles to administrator area.
            wp_register_style('select2-css', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css', false, '4.0.3');
            wp_enqueue_style('select2-css');


            // Add script to administrator area.
            wp_enqueue_script('select2-js', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js', ['jquery', 'jquery-ui-sortable'], null, false);
        });
    }


    /**
     * Header - Custom Meta Assets
     *
     */
    private function assetsHeader()
    {
        add_action('admin_head', function ()
        {
            echo file_get_contents(dirname(__FILE__). '/assets/metabox-styles.css');
        });
    }


    /**
     * Footer - Custom Meta Assets
     *
     */
    private function assetsFooter()
    {
        add_action('admin_footer', function ()
        {
            echo '<script>' . file_get_contents(dirname(__FILE__) . '/assets/metabox-scripts.js') . '</script>';
        },
            20
        );
    }


    /**
     * Remove the default Wordpress post custom metabox from
     * all Posts/Pages/CPT that have a Custom Metabox added.
     * This simplifies the user selections to only one area on
     * admin post area.
     *
     * @return void
     */
    private function removeWPFeatures()
    {
        // Remove Features.
        foreach ($this->settings['remove'] as $feature) {
            remove_post_type_support(get_post_type($this->postID), $feature);
        }


        // Remove Spacing Issue
        if (in_array('title', $this->settings['remove']) && in_array('editor', $this->settings['remove'])) {
            echo '<style>#post-body-content { display:none; }</style>';
        }
    }


    /**
     * Build the HTML for the Metabox fields defined by the $fields property.
     * Set up the nonce field, re-populate the input values and create.
     * Include minimal CSS/JS for making the metabox UI.
     *
     * @return void
     */
    private function initMetabox()
    {
        add_action('add_meta_boxes', function ()
        {
            // Add Metabox.
            add_meta_box(
                'custom-metabox-' . $this->settings['id'],
                $this->settings['title'],
                [$this, 'showMetaboxHTML'],
                null,
                $this->settings['location'],
                $this->settings['priority']
            );

            // Add dependency class to metabox container to initially hide.
            if (!empty($this->settings['dependency'])) {
                add_filter('postbox_classes_' . get_post_type($this->postID) . '_custom-metabox-' . $this->settings['id'], function ($classes)
                {
                    $classes[] = 'data-dependency-key';
                    return $classes;
                });
            }
        });
    }


    /**
     * Show Metabox Markup
     *
     */
    public function showMetaboxHTML()
    {
        // Add nonce for security and authentication.
        wp_nonce_field($this->nonceAction, $this->nonceName);

        // Condition Dependency.
        if (!empty($this->settings['dependency'])) {
            $condition  = ' data-dependency-key="' . $this->settings['dependency']['key'] . '"';
            $condition .= ' data-dependency-value=\'' . $this->settings['dependency']['value'] . '\'';
            $condition .= ' data-dependency-condition="' . $this->settings['dependency']['condition'] . '"';

            echo '<span data-key="custom-metabox-' . $this->settings['id'] . '" data-type="metabox" ' . $condition . '></span>';
        }

        // Show metabox instructions.
        if (!empty($this->settings['instructions'])) {
            echo '<div class="instructions">' . $this->settings['instructions'] . '</div>';
        }

        // Check if fields are empty.
        if (empty($this->fields)) {
            return;
        }

        $this->getFields();
    }


    /**
     * Meta Field Markup
     * @param  string $name, array $attributes, string $storedValue
     *
     */
    private function getFields()
    {
        // Show headers and fields.
        foreach ($this->fields as $name => $attributes) {
            // Subfields.
            if (!empty($attributes['subfields'])) {
                // Condition Dependency.
                $condition = '';

                if (!empty($attributes['dependency'])) {
                    $condition  = ' data-dependency-key="' . $attributes['dependency']['key'] . '"';
                    $condition .= ' data-dependency-value=\'' . $attributes['dependency']['value'] . '\'';
                    $condition .= ' data-dependency-condition="' . $attributes['dependency']['condition'] . '"';
                }

                // Field markup.
                echo '<div class="input-group type-object" data-key="' . $name . '" data-type="object" ' . $condition . '>';

                // Set label if exists.
                if (!empty($attributes['label'])) {
                    echo '<span class="label">' . $attributes['label'] . '</span>';
                }

                // Set description if exists.
                if (!empty($attributes['description'])) {
                    echo '<div class="description">' . $attributes['description'] . '</div>';
                }

                // Subfields.
                echo '<div data-subfields-container="' . $name . '" ' . ($attributes['repeater'] ? 'data-repeater="true"' : '') . '>';

                $count = 1;

                if ($attributes['repeater'] && !empty(reset($attributes['subfields'])['values'])) {
                    $count = count(reset($attributes['subfields'])['values']);
                }

                for ($i = 0; $i < $count; $i++) {
                    echo '<div data-subfields-parent="' . $name . '">';

                    foreach ($attributes['subfields'] as $subName => $subAttributes) {
                        // Update Subname.
                        if ($attributes['repeater']) {
                            $subName = $name . '[' . $i . '][' . $subName . ']';
                        } else {
                            $subName = $name . '[' . $subName . ']';
                        }

                        // Update Value.
                        $subAttributes['value'] = '';

                        if (!empty($subAttributes['values'][$i])) {
                            $subAttributes['value'] = $subAttributes['values'][$i];
                        }

                        $this->getFieldHTML($subName, $subAttributes);
                    }

                    if ($count > 1) {
                        echo '<button class="button-remove">&#10005;</button>';
                    }

                    echo '</div>';
                }

                // End of Input Area.
                echo '</div>';

                // If repeater.
                if ($attributes['repeater']) {
                    echo '<div class="button-controls"><button data-subfields-add="' . $name . '" class="button-primary">Add</button></div>';
                }

                // End of Field Group.
                echo '</div>';

                // Jump to next field.
                continue;
            }

            // Show Fields.
            $this->getFieldHTML($name, $attributes);
        }
    }


    /**
     * Meta Field Markup
     * @param  string $name, array $attributes, string $storedValue
     *
     */
    private function getFieldHTML($name, $attributes)
    {
        // Extract field attributes.
        extract($attributes);


        // HTML/Message Snippet.
        if (!empty($snippet)) {
            echo '<div class="snippet">' . $snippet . '</div>';
            return;
        }


        // Field exists.
        if (empty($type) || !file_exists(dirname(__FILE__) . '/fields/' . $type . '.php')) {
            echo '<div class="notification-error"><strong>Type</strong> is empty or field does not exist.</div>';
            return;
        }


        // Multiselect, append [] to name.
        if ($type == 'select-multiple') {
            $name .= '[]';
        }


        // Label html.
        $labelHTML = '';
        if (!empty($label)) {
            $requiredHTML = $required ? '<span class="required">*</span>' : '';
            $labelHTML    = '<label for="' . $name . '">' . $label . $requiredHTML . '</label>';
        }


        // Description html.
        $descriptionHTML = !empty($description) ? '<div class="description">' . $description . '</div>' : '';


        // Conditional dependency.
        $condition = '';
        if (!empty($dependency)) {
            $condition  = ' data-dependency-key="' . $dependency['key'] . '"';
            $condition .= ' data-dependency-value=\'' . $dependency['value'] . '\'';
            $condition .= ' data-dependency-condition="' . $dependency['condition'] . '"';
        }


        // Field group.
        echo '<div class="input-group type-' . $type . '" data-key="' . $name . '" data-type="' . $type . '" ' . $condition . '>';
        include dirname(__FILE__) . '/fields/' . $type . '.php';
        echo '</div>';
    }


    /**
     * Save Meta Field Inputs
     *
     */
    private function initSavePost()
    {
        add_action('save_post', function ()
        {
            // Validation Check.
            if (!$this->validateSaveRequest()) {
                return;
            }

            // Loop through Meta Fields.
            foreach ($this->fields as $name => $attributes) {
                // Check if key exists.
                if (!array_key_exists($name, $_POST)) {
                    continue;
                }

                // Callback.
                if (!$this->callback($name, $attributes)) {
                    continue;
                }

                // Subfields.
                if (!empty($attributes['subfields']) && is_array($_POST[$name])) {
                    $hasValue = false;

                    // If repeater check deeper array.
                    if ($attributes['repeater']) {
                        foreach ($_POST[$name] as &$inputGroups) {
                            foreach ($inputGroups as $inputName => &$inputValue) {
                                if (empty($inputValue)) {
                                    continue;
                                }

                                if (!empty($attributes['subfields'][$inputName]) &&
                                    $attributes['subfields'][$inputName]['type'] == 'select2-multiple'
                                ) {
                                    $inputValue = explode(',', $inputValue);
                                }

                                $hasValue = true;
                            }
                        }
                    } else {
                        foreach ($_POST[$name] as $inputName => &$inputValue) {
                            if (empty($inputValue)) {
                                continue;
                            }

                            if (!empty($attributes['subfields'][$inputName]) &&
                                $attributes['subfields'][$inputName]['type'] == 'select2-multiple'
                            ) {
                                $inputValue = explode(',', $inputValue);
                            }

                            $hasValue = true;
                        }
                    }

                    // Check if multidimensional array is empty.
                    if (!$hasValue) {
                        $_POST[$name] = '';
                    }
                }

                // Delete Post Meta Fields if Empty.
                if (empty($_POST[$name]) && $attributes['type'] != 'radio') {
                    delete_post_meta($this->postID, $name);
                    continue;
                }

                // select2-multiple: Convert Value to Array.
                if (!empty($attributes['type']) && $attributes['type'] == 'select2-multiple') {
                    $_POST[$name] = explode(',', $_POST[$name]);
                }

                // If is value is array
                if (is_array($_POST[$name])) {
                    $_POST[$name] = array_map('stripslashes_deep', $_POST[$name]);
                    $_POST[$name] = wp_slash(
                        json_encode(
                            $_POST[$name],
                            JSON_PRETTY_PRINT|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS
                        )
                    );

                    if (JSON_ERROR_NONE != json_last_error()) {
                        throw new \Exception('JSON encoding failed with error:' . json_last_error_msg());
                    }
                }

                // Update Post Meta Fields.
                update_post_meta($this->postID, $name, $_POST[$name]);
            }
        });
    }


    /**
     * Run through a series of tests to confirm that the save request
     * Is valid, including checking the nonce set up in metaboxHTML()
     *
     * @return bool does the save request validate?
     */
    private function validateSaveRequest()
    {
        // Check if $_POST request.
        if (empty($_POST)) {
            return false;
        }


        // Add nonce for security and authentication
        if (!isset($_POST[$this->nonceName])) {
            return false;
        }


        // Check if nonce is set & nonce is valid.
        if (!wp_verify_nonce($_POST[$this->nonceName], $this->nonceAction)) {
            return false;
        }


        // Check if user has permissions to save data.
        if (!current_user_can('edit_post', $this->postID)) {
            return false;
        }


        // Check if not an autosave.
        if (wp_is_post_autosave($this->postID)) {
            return false;
        }


        // Check if not a revision.
        if (wp_is_post_revision($this->postID)) {
            return false;
        }


        // Check if fields are empty.
        if (empty($this->fields)) {
            return false;
        }


        return true;
    }


    /**
     * Run through a series of tests to confirm that the metabox can be
     * added to the current post, based off of the defined settings.
     * Checks is Admin, Post Exists, Posts, Parent, Taxonomy,
     * and Template to display metabox.
     *
     * @return bool does the save request validate?
     */
    private function validateSettings()
    {
        // Check if should be on all.
        if (
            empty($this->settings['post_type']) &&
            empty($this->settings['post']) &&
            empty($this->settings['parent']) &&
            empty($this->settings['taxonomy']) &&
            empty($this->settings['template'])
        ) {
            return true;
        }


        // Check if new page & post type is set.
        if (
            empty($this->postID) &&
            !empty($_GET['post_type']) &&
            in_array($_GET['post_type'], $this->settings['post_type'])
        ) {
            return true;
        }


        // Check if Post(s).
        if (in_array($this->postID, $this->settings['post'])) {
            return true;
        }


        // Check if Post Type.
        if (in_array(get_post_type($this->postID), $this->settings['post_type'])) {
            return true;
        }


        // Check if Parent.
        if (in_array(wp_get_post_parent_id($this->postID), $this->settings['parent'])) {
            return true;
        }


        // Check if Template.
        if (in_array(get_page_template_slug($this->postID), $this->settings['template'])) {
            return true;
        }


        // Check if settings.taxonomy is set.
        foreach ($this->settings['taxonomy'] as $taxonomyAllowed => $termsAllowed) {

            // Get post terms based on taxonomy.
            $postTerms = get_the_terms($this->postID, $taxonomyAllowed);

            // Check for if post terms.
            if (is_wp_error($postTerms) || empty($postTerms)) {
                continue;
            }

            // Loop through post terms in taxonomy.
            foreach ($postTerms as $postTerm) {
                // Check terms compared to user inputted array of values.
                if (in_array($postTerm->slug, $termsAllowed) ||
                    in_array($postTerm->term_id, $termsAllowed) ||
                    in_array($postTerm->name, $termsAllowed)
                ) {
                    return true;
                }
            }
        }


        return false;
    }


    /**
     * Callback Method
     *
     */
    private function callback($name, $attributes)
    {
        // Check if callback exists\Check if is callable.
        if (empty($attributes['callback']) || !is_callable($attributes['callback'])) {
            return true;
        }


        // Data attribute of callback.
        $data = [
            'postID'     => $this->postID,
            'name'       => $name,
            'value'      => !empty($_POST[$name]) ? $_POST[$name] : '',
            'attributes' => $attributes
        ];


        // Callback Function, pass in value as data argument.
        return (call_user_func($attributes['callback'], $data) === false) ? false : true;
    }
}


/**
 * Add Plugin Callback Methods.
 * FYI - I know this is a no-no and against PSR2 Standards... :(
 */
foreach (glob(__DIR__ . '/plugins/*.php') as $filename) {
    require_once $filename;
}

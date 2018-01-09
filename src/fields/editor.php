<?php

echo $labelHTML;
echo $descriptionHTML;

wp_editor(
    esc_textarea($value),
    'he-editor-' . rand(1, 1000000),
    array(
        'textarea_name'  => $name,
        'media_buttons'  => true,
        'editor_height'  => 200,
        'tinymce'        => true,
        'quicktags'      => true
    )
);

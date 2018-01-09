<?php

echo $labelHTML;
echo $descriptionHTML;

$attributes  = $style ? ' style="' . $style . '"' : '';
$attributes .= $readonly ? ' readonly' : '';
$attributes .= $required ? ' required' : '';
    
?>

<textarea name="<?php echo $name; ?>" <?php echo $attributes; ?>><?php echo esc_textarea($value); ?></textarea>

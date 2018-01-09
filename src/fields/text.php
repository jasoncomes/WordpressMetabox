<?php

echo $labelHTML;
echo $descriptionHTML;

$attributes  = $value ? ' value="' . esc_attr($value) . '"' : '';
$attributes .= $style ? ' style="' . $style . '"' : '';
$attributes .= $placeholder ? ' placeholder="' . $placeholder . '"' : '';
$attributes .= $pattern ? ' pattern="' . $pattern . '"' : '';
$attributes .= $readonly ? ' readonly' : '';
$attributes .= $maxlength ? ' maxlength="' . $maxlength . '"' : '';
$attributes .= $required ? ' required' : '';

?>

<input type="text" name="<?php echo $name; ?>" <?php echo $attributes; ?>/>

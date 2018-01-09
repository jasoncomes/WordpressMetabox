<?php

echo $labelHTML;
echo $descriptionHTML;

$attributes  = $value ? ' value="' . esc_attr($value) . '"' : '';
$attributes .= $style ? ' style="' . $style . '"' : '';
$attributes .= $placeholder ? ' placeholder="' . $placeholder . '"' : '';
$attributes .= $pattern ? ' pattern="' . $pattern . '"' : '';
$attributes .= $readonly ? ' readonly' : '';
$attributes .= $maxlength ? ' maxlength="' . $maxlength . '"' : '';
$attributes .= $min ? ' min="' . $min . '"' : '';
$attributes .= $max ? ' max="' . $max . '"' : '';
$attributes .= $maxlength ? ' maxlength="' . $maxlength . '"' : '';
$attributes .= $required ? ' required' : '';

?>

<input type="number" name="<?php echo $name; ?>" <?php echo $attributes; ?>/>

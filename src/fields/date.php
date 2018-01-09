<?php

echo $labelHTML;
echo $descriptionHTML;

$attributes  = $value ? ' value="' . $value . '"' : '';
$attributes .= $required ? ' required' : '';
$attributes .= $placeholder ? ' placeholder="' . $pattern . '"' : '';
$attributes .= $pattern ? ' pattern="' . $pattern . '"' : '';
$attributes .= $style ? ' style="' . $style . '"' : '';
$attributes .= $readonly ? ' readonly' : '';

?>

<input type="date" name="<?php echo $name; ?>" <?php echo $attributes; ?> />

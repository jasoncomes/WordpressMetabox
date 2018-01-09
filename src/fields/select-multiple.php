<?php

if (!empty($label)) {
    echo '<label for="' . $name . '">' . $label . ($required ? '<span class="required">*</span>' : '') . '</label>';
}

echo $descriptionHTML;

$valueArray = is_array($value) ? $value : json_decode($value, true);
$attributes = $style ? ' style="' . $style . '"' : '';
$attributes .= $required ? ' required' : '';

?>

<select name="<?php echo $name; ?>" multiple="multiple" <?php echo $attributes; ?>>

    <?php
    foreach ($options as $optionLabel => $optionValue) :
        $selected = !empty($valueArray) && in_array($optionValue, $valueArray) ? 'selected="selected"' : '';
    ?>

    <option value="<?php echo $optionValue; ?>" <?php echo $selected; ?>><?php echo $optionLabel; ?></option>
    
    <?php endforeach; ?>

</select>

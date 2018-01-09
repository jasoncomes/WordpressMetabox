<?php

echo $labelHTML;
echo $descriptionHTML;

$attributes  = $style ? ' style="' . $style . '"' : '';
$attributes .= $required ? ' required' : '';

?>

<select name="<?php echo $name; ?>" <?php echo $attributes; ?>>

    <?php
    foreach ($options as $optionLabel => $optionValue) :
        $selected = $optionValue == $value ? 'selected="selected"' : '';
    ?>

   <option value="<?php echo $optionValue; ?>" <?php echo $selected; ?>><?php echo $optionLabel; ?></option>

    <?php endforeach; ?>

</select>

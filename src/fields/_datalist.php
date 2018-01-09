<?php

echo $labelHTML;
echo $descriptionHTML;

$attributes  = $value ? ' value="' . $value . '"' : '';
$attributes .= $placeholder ? ' placeholder="' . $placeholder . '"' : '';
$attributes .= $maxlength ? ' maxlength="' . $maxlength . '"' : '';
$attributes .= $required ? ' required' : '';
$attributes .= $maxlength ? ' maxlength="' . $maxlength . '"' : '';
$attributes .= $readonly ? ' readonly' : '';
$attributes .= ' class="input-datalist"';

?>

<input list="<?php echo $name; ?>" name="<?php echo $name; ?>"  <?php $attributes; ?> />

<datalist id="<?php echo $name; ?>">

    <?php
    foreach ($options as $optionLabel => $optionValue) :
        $selected = $optionValue == $value ? ' selected="selected"' : '';
    ?>

    <option value="<?php echo $optionValue; ?>" <?php echo $selected; ?>><?php echo $optionLabel; ?></option>

    <?php endforeach; ?>

</datalist>

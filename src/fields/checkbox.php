<?php

echo $descriptionHTML;

$attributes = !empty($value) ? ' checked="checked"' : '';

?>

<label for="<?php echo $name; ?>">

    <input type="hidden" name="<?php echo $name; ?>" value="0" />
    <input type="checkbox" id="<?php echo $name; ?>" name="<?php echo $name; ?>" value="1" <?php echo $attributes; ?>/>

    <?php echo $label; ?>

</label>

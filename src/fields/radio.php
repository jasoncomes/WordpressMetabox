<?php

if (!empty($label)) {
    echo '<span class="label">' . $label . ($required ? '<span class="required">*</span>' : '') . '</span>';
}

echo $description;

foreach ($options as $optionLabel => $optionValue) :
    $checked = $optionValue == $value ? ' checked="checked"' : '';
    $id      = $name . '-' . $optionValue;

    ?>

    <label for="<?php echo $id; ?>">

    <input type="radio" 
           name="<?php echo $name; ?>"
           id="<?php echo $id; ?>" 
           value="<?php echo $optionValue; ?>" 
            <?php echo $checked; ?> />
           
    <?php echo $optionLabel; ?>
        
    </label>

<?php endforeach;

<?php
   
echo $labelHTML;
echo $descriptionHTML;

$valueArray  = is_array($value) ? $value : json_decode($value, true);
$valueString = !empty($valueArray) ? implode(',', $valueArray) : '';
$attributes  = $required ? ' required' : '';

?>

<input name="<?php echo $name; ?>" value="<?php echo $valueString; ?>" type="hidden" tabindex="-1" />

<select style="width: 100%;" multiple="multiple" <?php echo $attributes; ?>>
    
    <?php
    if (!empty($valueArray)) :
        foreach ($valueArray as $value) :
            $optionLabel = array_search($value, $options);
    ?>

            <option value="<?php echo $value; ?>" selected="selected"><?php echo $optionLabel; ?></option>
            <?php unset($options[$optionLabel]); ?>
    
    <?php
        endforeach;
    endif;

    foreach ($options as $optionLabel => $optionValue) :
        if (empty($optionValue)) {
            continue;
        }
    ?>

    <option value="<?php echo $optionValue; ?>"><?php echo $optionLabel; ?></option>

    <?php endforeach; ?>

</select>

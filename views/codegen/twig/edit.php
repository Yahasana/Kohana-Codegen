<form action="" method="post" name="form1" id="form1">
<input type="hidden" name="__v_state__" value="{{ __v_state__ }}" />
<?php
    $key_id = key($columns);
    foreach($columns as $key => $column)
    {
        if($key == $key_id)
        {
            ?>
<input type="hidden" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="<?php echo '{{ '.$key.' }}'; ?>" />
<?php
            continue;
        }
        switch($column['data_type'])
        {
            case 'int':
            case 'int unsigned':
            case 'tinyint':
            case 'tinyint unsigned':?>

<label for="<?php echo $key; ?>"><?php echo '{{ GI18N.'.ucfirst(Inflector::camelize(Inflector::humanize($key))).' }}'; ?></label>
<input type="text" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="<?php echo '{{ '.$key.' }}'; ?>" /><?php
                break;
            case 'varchar':
                if($column['character_maximum_length'] <= 256)
                { ?>

<label for="<?php echo $key; ?>"><?php echo '{{ GI18N.'.ucfirst(Inflector::camelize(Inflector::humanize($key))).' }}'; ?></label>
<input type="text" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="<?php echo '{{ '.$key.' }}'; ?>" /><?php
                }
                else
                {?>

<label for="<?php echo $key; ?>"><?php echo '{{ GI18N.'.ucfirst(Inflector::camelize(Inflector::humanize($key))).' }}'; ?></label>
<textarea name="<?php echo $key; ?>" id="<?php echo $key; ?>"><?php echo '{{ '.$key.' }}'; ?></textarea><?php
                }
                break;
            case 'text': ?>

<label for="<?php echo $key; ?>"><?php echo '{{ GI18N.'.ucfirst(Inflector::camelize(Inflector::humanize($key))).' }}'; ?></label>
<textarea name="<?php echo $key; ?>" id="<?php echo $key; ?>"><?php echo '{{ '.$key.' }}'; ?></textarea><?php
                break;
            default: ?>

<label for="<?php echo $key; ?>"><?php echo '{{ GI18N.'.ucfirst(Inflector::camelize(Inflector::humanize($key))).' }}'; ?></label>
<input type="text" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="<?php echo '{{ '.$key.' }}'; ?>" /><?php
                break;
        }
    }
?>

</form>

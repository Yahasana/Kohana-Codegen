<?php echo '<?php'; ?>

    $GI18N = I18n::load(I18n::$lang);
<?php
    echo '?>';
?><form action="" method="post" name="form1" id="form1">
<input type="hidden" name="__v_state__" value="<?php echo '<?php echo md5(REQUEST_TIME); ?>'; ?>" />
<?php
    $key_id = key($columns);
    foreach($columns as $key => $column)
    {
        if($key == $key_id)
        {
            ?>
<input type="hidden" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="<?php echo '<?php echo $'.$key.'; ?>'; ?>" />
<?php
            continue;
        }
        switch($column['data_type'])
        {
            case 'int':
            case 'int unsigned':
            case 'tinyint':
            case 'tinyint unsigned':?>

<label for="<?php echo $key; ?>"><?php echo '<?php echo $GI18N[\''.ucfirst(Inflector::humanize($key)).'\']; ?>'; ?></label>
<input type="text" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="<?php echo '<?php if(isset($'.$key.')) echo $'.$key.'; ?>'; ?>" /><?php
                break;
            case 'varchar':
                if($column['character_maximum_length'] <= 256)
                { ?>

<label for="<?php echo $key; ?>"><?php echo '<?php echo $GI18N[\''.ucfirst(Inflector::humanize($key)).'\']; ?>'; ?></label>
<input type="text" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="<?php echo '<?php if(isset($'.$key.')) echo $'.$key.'; ?>'; ?>" /><?php
                }
                else
                {?>

<label for="<?php echo $key; ?>"><?php echo '<?php echo $GI18N[\''.ucfirst(Inflector::humanize($key)).'\']; ?>'; ?></label>
<textarea name="<?php echo $key; ?>" id="<?php echo $key; ?>"><?php echo '<?php if(isset($'.$key.')) echo $'.$key.'; ?>'; ?></textarea><?php
                }
                break;
            case 'text': ?>

<label for="<?php echo $key; ?>"><?php echo '<?php echo $GI18N[\''.ucfirst(Inflector::humanize($key)).'\']; ?>'; ?></label>
<textarea name="<?php echo $key; ?>" id="<?php echo $key; ?>"><?php echo '<?php if(isset($'.$key.')) echo $'.$key.'; ?>'; ?></textarea><?php
                break;
            default: ?>

<label for="<?php echo $key; ?>"><?php echo '<?php echo $GI18N[\''.ucfirst(Inflector::humanize($key)).'\']; ?>'; ?></label>
<input type="text" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="<?php echo '<?php if(isset($'.$key.')) echo $'.$key.'; ?>'; ?>" /><?php
                break;
        }
    }
?>

</form>

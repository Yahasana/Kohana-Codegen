<?php  echo '<?php'; ?>

    $GI18N = I18n::load(I18n::$lang);
<?php
    echo '?>';
?><ul>
<?php
    $key_id = key($columns);
    foreach($columns as $key => $column)
    {
        if($key == $key_id) continue;
?>
<li><span><?php echo '<?php echo $GI18N[\''.ucfirst(Inflector::humanize($key)).'\']; ?>'; ?></span><?php echo '<?php echo $'.$key.'; ?>'; ?></li>
<?php
    }
?>
</ul>

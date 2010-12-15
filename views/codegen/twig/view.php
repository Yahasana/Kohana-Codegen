<ul>
<?php
    $key_id = key($columns);
    foreach($columns as $key => $column)
    {
        if($key == $key_id) continue;
?>
<li><span><?php echo '{{ GI18N.'.ucfirst(Inflector::camelize(Inflector::humanize($key))).' }}'; ?></span><?php echo '{{ '.$key.' }}'; ?></li>
<?php
    }
?>
</ul>

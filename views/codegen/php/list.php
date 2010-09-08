<?php echo '<?php'; ?>

    $GI18N = I18n::load(I18n::$lang);
<?php
    echo '?>';
?><table><caption><?php echo $table; ?></caption>
<thead><tr><?php
    $key_id = key($columns);
    $thead      = '';
    $tbody      = "<?php\nforeach(\$".Inflector::plural($table)." as \$row)\n{\n?><tr>";
    foreach($columns as $key => $column)
    {
        $thead .= "\n<th><?php echo \$GI18N['".ucfirst(Inflector::humanize($key))."']; ?></th>";
        if($key == $key_id)
        {
            $tbody .= "\n<th><?php echo \$row['".$key."']; ?></th>";
        }
        else
        {
            $tbody .= "\n<td><?php echo \$row['".$key."']; ?></td>";
        }
    }
    echo $thead; ?>

</tr></thead>
<tbody><?php
    echo $tbody."\n</tr><?php\n}\n?>";
?>

</tbody></table>

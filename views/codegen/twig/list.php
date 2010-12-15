<table><caption><?php echo $table; ?></caption>
<thead><tr><?php
    $key_id = key($columns);
    $thead      = '';
    $tbody      = "{% for $table in ".Inflector::plural($table)." %}<tr>";
    foreach($columns as $key => $column)
    {
        $thead .= "\n<th>{{ GI18N.".ucfirst(Inflector::camelize(Inflector::humanize($key)))." }}</th>";
        if($key == $key_id)
        {
            $tbody .= "\n<th>{{ $table.".$key." }}</th>";
        }
        else
        {
            $tbody .= "\n<td>{{ $table.".$key." }}</td>";
        }
    }
    echo $thead; ?>

</tr></thead>
<tbody><?php
    echo $tbody."\n</tr>{% endfor %}";
?>

</tbody></table>

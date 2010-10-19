<!DOCTYPE html>
<html>
<style>
    html{text-align:center;}
    body{margin:1em auto;width:90%}
    table{border:1px solid #eee;border-width:0 1px;text-align:left}
    a{text-decoration:none}
    caption{background:#A52A2A;color:#00FFA9}
    caption a{margin:0 1em}
    caption i{color:#FFA07A;padding:5px;display:block}
    table td{border-bottom:1px solid #eee;padding:0 2em}
    thead th{border-bottom:1px solid #eee;text-align:center;background:#222;color:#eee}
    tbody th{border:1px solid #bbb;border-width:0 0 1px 0;text-align:left;background:#eee;padding-left:5px}
    tfoot th,tfoot td{background:#FFE4C4;padding:1em 5px}
    tfoot td{text-align:center}
    .good{color:green}.notyet{color:#DAA520}.noexist{color:red}
</style>
<body>
<table>
<caption><h3>Kohana Codegen generated for <?php echo $module; ?></h3>
<?php 
    foreach($modules as $m)
    {
        if($m === $module) continue;
        echo '<a href="'.URL::site('codegen?m='.$m).'">'.$m.'</a>';
    }
?><i>&#9745 - successfully, &oplus; - disabled, &#9746 - driver not impletement, &#10008; - driver not exist</i></caption>
<thead><tr><th>Data Tables</th><th>Controllers</th><th>Models</th><th>Views</th><th>Themes</th><th>I18n</th><th>API</th></tr></thead><tbody><?php

    foreach($tables as $table => $data)
    {
        echo '<tr><th>'.$table.'</th><td>'
            .$data['controller'].'</td><td>'
            .$data['model'].'</td><td>'
            .$data['view'].'</td><td>'
            .$data['theme'].'</td><td>'
            .$data['i18n'].'</td><td>'
            .$data['api'].'</td></tr>';
    }

?></tbody>
<tfoot>
    <tr><th>Powered By <a href="http://github.com/Yahasana/Kohana-Codegen">Kohana Codegen</a></th>
    <td colspan="5">Copyright &copy; sumh &lt;oalite at gmail dot com&gt;, version 1.0 <sup style="color:#A52A2A">&alpha;</sup></td>
    </tr>
</tfoot>
</table>
</body>
</html>
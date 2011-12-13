<?php
echo "<?php defined('SYSPATH') or die('No direct script access.');\n$description\n";
?>
class <?= $class ?> extends <?= $extends ?> {

    protected $_db = '<?= $database ?>';

    public function get($<?= $key_id ?>)
    {
        return ctype_digit((string) $<?= $key_id ?>)
            ? DB::select('<?= implode("','", $columns) ?>')
                ->from('<?= $table_old ?>')
                ->where('<?= $key_id ?>', '=', $<?= $key_id ?>)
                ->execute($this->_db)
                ->current()
            : NULL;
    }

    /**
     * Insert <?= $table."\n" ?>
     *
     * @access	public
     * @param	array	$params<?= $comment."\n" ?>
     * @return	mix     Validated data or validate object
     */
    public function append(array $params = NULL)
    {
        $params === NULL AND $params = $_POST ?: $_GET;

        <?= $datatime."\n        " ?>$valid = Validate::factory($params)<?= $rules ?>;

        <?= $rule_insert."\n" ?>

        foreach($rules as $field => $rule)
            foreach($rule as $r => $p)
                $valid->rule($field, $r, $p);

        if($valid->check())
        {
            $valid = $valid->as_array();

            foreach($valid as $key => $val)
            {
                if($val === '') $valid[$key] = NULL;
            }

            // $valid['insert_by']   = $_SESSION['user'];
            $valid['insert_time']   = $_SERVER['REQUEST_TIME'];

            $insert = DB::insert('<?= $table_old ?>', array_keys($valid))
                ->values(array_values($valid))
                ->execute($this->_db);

            $valid                 += $params;
            $valid['<?= $key_id ?>'] = $insert[0];
            $valid['affected_rows'] = $insert[1];
        }

        // Validation data, or collection of the errors
        return $valid;
    }

    /**
     * Update <?= $table."\n" ?>
     *
     * @access	public
     * @param	int	    $<?= $key_id."\n" ?>
     * @param	array	$params<?= $comment."\n" ?>
     * @return	mix     Validated data or validate object
     */
    public function update($<?= $key_id ?>, array $params = NULL)
    {
        $params === NULL AND $params = $_POST ?: $_GET;

        <?= $datatime."\n        " ?>$valid = Validate::factory($params);

        <?= $rule_update."\n" ?>

        foreach($rules as $field => $rule)
            foreach($rule as $r => $p)
                $valid->rule($field, $r, $p);

        if($valid->check())
        {
            $valid = $valid->as_array();

            foreach($valid as $key => $val)
            {
                if($val === '') $valid[$key] = NULL;
            }

            //$valid['update_by']   = $_SESSION['user'];
            $valid['update_time']   = $_SERVER['REQUEST_TIME'];

            $valid['affected_rows'] = DB::update('<?= $table_old ?>')
                ->set($valid)
                ->where('<?= $key_id ?>', '=', $<?= $key_id ?>)
                ->execute($this->_db);

            $valid += $params;
        }

        // Validation data, or collection of the errors
        return $valid;
    }

    public function delete($<?= $key_id ?>)
    {
        return ctype_digit((string) $<?= $key_id ?>)
            ? DB::delete('<?= $table_old ?>')
                ->where('<?= $key_id ?>', '=', $<?= $key_id ?>)
                ->execute($this->_db)
            : NULL;
    }

    /**
     * List <?= $table ?>s
     *
     * @access	public
     * @param	array	    $params
     * @param	Pagination	$pagination	default [ NULL ] passed by reference
     * @param	boolean	    $calc_total	default [ TRUE ] is needed to caculate the total records for pagination
     * @return	array       array('<?= $table ?>s' => data, 'orderby' => $params['orderby'], 'pagination' => $pagination)
     */
    public function lists(array $params, $pagination = NULL, $calc_total = TRUE)
    {
        $pagination instanceOf Pagination OR $pagination = new Pagination;

        $sql = 'FROM `<?= $table_old ?>` ';

        // Customize where from params
        //$sql .= 'WHERE ... '

        // caculte the total rows
        if($calc_total === TRUE)
        {
            $pagination->total_items = $this->_db->query(
                Database::SELECT, 'SELECT COUNT(`<?= $key_id ?>`) num_rows '.$sql, FALSE
            )->get('num_rows');

            $data['pagination'] = $pagination;

            if($pagination->total_items === 0)
            {
                $data['<?= $table ?>s'] = array();
                isset($params['orderby']) AND $data['orderby'] = $params['orderby'];
                return $data;
            }
        }

        // Customize order by from params
        if(isset($params['orderby']))
        {
            switch($params['orderby'])
            {
                <?php
                foreach($columns as $key)
                {?>
                case '<?= $key ?>':
                    $sql .= ' ORDER BY <?= $key ?> ';
                    break;<?php
                }?>
                default:
                    $params['orderby'] = '<?= $key_id ?>';
                    $sql .= ' ORDER BY <?= $key_id ?> ';
                    break;
            }

            // e.g. ?orderby=<?= $key_id ?>&sort=asc
            $sql .= isset($params['sort']) ? $params['sort'] : 'DESC';

            // Tell the view orderby which field
            $data['orderby'] = $params['orderby'];
        }

        $sql .= " LIMIT {$pagination->offset}, {$pagination->items_per_page}";

        $data['<?= $table ?>s'] = $this->_db->query(Database::SELECT, 'SELECT * '.$sql, FALSE);

        return $data;
    }

} // END <?= $class."\n" ?>

<?php


namespace Microweber;


class Medium
{


    public $app;

    function __construct($app = null)
    {


        if (!is_object($this->app)) {

            if (is_object($app)) {
                $this->app = $app;
            } else {
                $this->app = mw('application');
            }

        }


        if (!defined("MW_DB_TABLE_MEDIUM")) {
            define('MW_DB_TABLE_MEDIUM', MW_TABLE_PREFIX . 'medium');
            $this->db_init();
        }

    }


    public function add_medium_to_content($content_id)
    {
        $id = $this->app->user->is_admin();
        if ($id == false) {
            return;
            mw_error('Error: not logged in as admin.' . __FILE__ . __LINE__);
        }
        $content_id = intval($content_id);
        if ($content_id == 0) {
            return;
        }

        $db = $this->app->db;


        $medim_table = MW_DB_TABLE_MEDIUM;

        if (isset($_REQUEST['medium_remote_url']) and isset($_REQUEST['medium_remote_selector'])) {
            $get = $db->get('one=1&table=medium&content_id=' . $content_id);
            $save = array();
            $save['content_id'] = $content_id;
            if (isset($get['id'])) {
                $save['id'] = $get['id'];
            }
            $save['content_id'] = $content_id;
            $save['url'] = $_REQUEST['medium_remote_url'];
            $save['selector'] = $_REQUEST['medium_remote_selector'];
            $s = $db->save($medim_table, $save);


        }
    }

    function get_settings($content_id)
    {
        $get = $this->app->db->get('limit=1&one=1debug=1&table=medium&content_id=' . $content_id);
        return $get;
    }


    function pull_content($params)
    {

        if (isset($params['id'])) {
            $content_id = intval($params['id']);


            if ($content_id != 0) {


                $function_cache_id = 'pull_content_' . $content_id;

                $cache_content = $this->app->cache->get($function_cache_id, 'medium', 6 * 300);
                //check if content has been cached in the last 30 min
                if (($cache_content) != false) {

                    return $cache_content;
                }

                $get = $this->app->db->get('limit=1&one=1debug=1&table=medium&content_id=' . $content_id);

                if (!empty($get) and isset($get['id']) and $get['url'] and $get['selector']) {

                    $pull = @file_get_contents($get['url']);


                    if ($pull != false and $pull != '') {

                        $new_body = $this->app->parser->get_html($pull, trim($get['selector']));
                        if ($new_body != false) {
                            $cont = array();
                            $cont['id'] = $content_id;
                            $cont['content'] = $new_body;
                            $cont['allow_html'] = $new_body;
                            //saves the new content in the db
                            $s = $this->app->db->save('content', $cont);
                            $this->app->cache->delete('content');
                        }
                    }

                }
                $this->app->cache->save($content_id, $function_cache_id, $cache_group = 'medium');

            }

        }


    }

    public function db_init()
    {
        $function_cache_id = false;

        $args = func_get_args();

        foreach ($args as $k => $v) {
            $function_cache_id = $function_cache_id . serialize($k) . serialize($v);
        }
        $function_cache_id = 'module_' . __CLASS__ . __FUNCTION__ . crc32($function_cache_id);

        $cache_content = $this->app->cache->get($function_cache_id, 'db');

        if (($cache_content) != false) {
            return $cache_content;
        }

        $table_name = MW_DB_TABLE_MEDIUM;

        $fields_to_add = array();

        $fields_to_add[] = array('updated_on', 'datetime default NULL');
        $fields_to_add[] = array('created_on', 'datetime default NULL');
        $fields_to_add[] = array('expires_on', 'datetime default NULL');
        $fields_to_add[] = array('created_by', 'int(11) default NULL');
        $fields_to_add[] = array('edited_by', 'int(11) default NULL');
        $fields_to_add[] = array('url', 'TEXT default NULL');
        $fields_to_add[] = array('selector', 'TEXT default NULL');
        $fields_to_add[] = array('content_id', 'int(11) default NULL');
        $fields_to_add[] = array('content', 'LONGTEXT default NULL');
        $fields_to_add[] = array('is_active', "char(1) default 'y'");
        $this->app->db->build_table($table_name, $fields_to_add);
        $this->app->cache->save($fields_to_add, $function_cache_id, $cache_group = 'db');
        return true;

    }
    function print_ui_field($content)
    {
        $cont_id = 0;
        if (isset($content['id'])) {
            $cont_id = $content['id'];
        }


        print '<module type="medium" view="add_content_url" content_id="' . $cont_id . '" />';
    }


}
<?php
$data = array();


if (isset($params['content_id'])) {


    $data = mw_medium_get_settings_content($params['content_id']);

}
if (!isset($data['url'])) {
    $data['url'] = '';
}
if (!isset($data['selector'])) {
    $data['selector'] = '';
}
?>

<div class="mw-ui-field-holder">
    <label class="mw-ui-label">
        <?php _e("Remote content url"); ?>
        <small class="mw-help" data-help="If you add url the content of this page will be replaced by it">(?)</small>
    </label>
    <input name="medium_remote_url" class="mw-ui-field" type="text" value="<?php print $data['url']; ?>"/>
</div>

<div class="mw-ui-field-holder">
    <label class="mw-ui-label">
        <?php _e("Remote html selector"); ?>
        <small class="mw-help" data-help=".class, #id, etc...">(?)</small>
    </label>
    <input name="medium_remote_selector" class="mw-ui-field" type="text" value="<?php print $data['selector']; ?>"/>
</div>

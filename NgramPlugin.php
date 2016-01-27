<?php
class NgramPlugin extends Omeka_Plugin_AbstractPlugin
{
    protected $_hooks = array(
        'install',
        'uninstall',
        'config_form',
        'config',
        'define_acl',
    );

    protected $_filters = array('admin_navigation_main');

    public function hookInstall()
    {
        $db = get_db();
        $db->query(<<<SQL
CREATE TABLE IF NOT EXISTS `{$db->prefix}ngram_corpora` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `query` text COLLATE utf8_unicode_ci,
  `text_element_id` int(10) unsigned NOT NULL,
  `sequence_member_element_id` int(10) unsigned NOT NULL,
  `sequence_member_pattern` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sequence_member_range` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `valid_items` text COLLATE utf8_unicode_ci,
  `invalid_items` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SQL
        );
    }

    public function hookUninstall()
    {
        $db = get_db();
        $db->query("DROP TABLE IF EXISTS `{$db->prefix}ngram_corpora`");

        delete_option('ngram_text_element_id');
    }

    public function hookConfigForm()
    {
        $db = get_db();
        $select = $db->select()
            ->from(
                array('element_sets' => $db->ElementSet),
                array('element_set_name' => 'element_sets.name')
            )->join(
                array('elements' => $db->Element),
                'element_sets.id = elements.element_set_id',
                array('element_id' =>'elements.id',
                'element_name' => 'elements.name')
            )->joinLeft(
                array('item_types_elements' => $db->ItemTypesElements),
                'elements.id = item_types_elements.element_id',
                array()
            )->where('element_sets.record_type IS NULL OR element_sets.record_type = "Item"')
            ->order(array('element_sets.name', 'elements.name'));
        $elements = $db->fetchAll($select);

        $options = array('' => __('Select Below'));
        foreach ($elements as $element) {
            $optGroup = __($element['element_set_name']);
            $value = __($element['element_name']);
            $options[$optGroup][$element['element_id']] = $value;
        }

        $view = get_view();
        include 'config_form.php';
    }

    public function hookConfig($args)
    {
        set_option('ngram_text_element_id', $args['post']['text_element_id']);
    }

    public function hookDefineAcl($args)
    {
        $args['acl']->addResource('Ngram_Index');
    }

    public function filterAdminNavigationMain($nav)
    {
        $nav[] = array(
            'label' => __('Ngram'),
            'uri' => url('ngram'),
            'resource' => ('Ngram_Index'),
        );
        return $nav;
    }
}

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
CREATE TABLE IF NOT EXISTS `{$db->prefix}ngram_corpus` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `query` text COLLATE utf8_unicode_ci,
  `sequence_element_id` int(10) unsigned NOT NULL,
  `sequence_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sequence_range` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `items` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SQL
        );
    }

    public function hookUninstall()
    {
        $db = get_db();
        $db->query("DROP TABLE IF EXISTS `{$db->prefix}ngram_corpus`");

        delete_option('ngram_text_element_id');
    }

    public function hookConfigForm()
    {
        $elementOptions = get_db()->getTable('NgramCorpus')->getElementsForSelect();
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
            'uri' => url('ngram/index'),
            'resource' => ('Ngram_Index'),
        );
        return $nav;
    }
}

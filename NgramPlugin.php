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
    }

    public function hookConfigForm()
    {}

    public function hookConfig()
    {}

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

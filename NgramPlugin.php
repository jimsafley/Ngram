<?php
class NgramPlugin extends Omeka_Plugin_AbstractPlugin
{
    protected $_hooks = array('install', 'uninstall', 'define_acl');

    protected $_filters = array('admin_navigation_main');

    public function hookInstall()
    {}

    public function hookUninstall()
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

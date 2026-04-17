<?php
if (!class_exists('DolibarrModules')) {
    require_once DOL_DOCUMENT_ROOT.'/core/modules/DolibarrModules.class.php';
}

class modChangeInvoiceRef extends DolibarrModules
{
    public function __construct($db)
    {
        global $langs, $conf;
        $this->db = $db;
        $this->numero = 136392;
        $this->rights_class = 'changeinvoiceref';
        $this->family = 'invoicing';
        $this->module_position = '50';
        $this->name = preg_replace('/^mod/i', '', get_class($this));
        $this->description = "Change Invoice REF";
        $this->descriptionlong = "Module to change invoice reference for draft invoices";
        $this->editor_name = 'Daxit Solutions';
        $this->editor_url = 'https://daxit.be';
        $this->version = '1.0.2';
        $this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
        $this->picto = 'generic';
        $this->module_parts = array(
            'hooks' => array('invoicecard')
        );
        $this->dirs = array();
        $this->config_page_url = array("setup.php@changeinvoiceref");
        $this->depends = array();
        $this->requiredby = array();
        $this->conflictwith = array();
        $this->langfiles = array("changeinvoiceref@changeinvoiceref");
        $this->phpmin = array(7, 2);
        $this->need_dolibarr_version = array(16, 0);
        $this->warnings_activation = array();
        $this->warnings_activation_ext = array();
        $this->const = array();
        $r = 0;
        $this->rights = array();
        $r++;
        $this->rights[$r][0] = $this->numero.$r;
        $this->rights[$r][1] = 'Change invoice reference';
        $this->rights[$r][3] = 0;
        $this->rights[$r][4] = 'change';
        $this->rights[$r][5] = '';
        $this->menu = array();
    }

    public function init($options = '')
    {
        $result = $this->_load_tables('/install/mysql/', 'changeinvoiceref');
        if ($result < 0) {
            return -1;
        }
        $sql = array();
        return $this->_init($sql, $options);
    }

    public function remove($options = '')
    {
        $sql = array();
        return $this->_remove($sql, $options);
    }
}

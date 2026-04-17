<?php
$res = 0;
if (!$res && file_exists("../main.inc.php")) $res = @include "../main.inc.php";
if (!$res && file_exists("../../main.inc.php")) $res = @include "../../main.inc.php";
if (!$res && file_exists("../../../main.inc.php")) $res = @include "../../../main.inc.php";
if (!$res) die("Include of main fails");

require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';

$langs->loadLangs(array("admin", "changeinvoiceref@changeinvoiceref"));

if (!$user->admin) {
    accessforbidden();
}

llxHeader('', $langs->trans("ChangeInvoiceRefSetup"));

$linkback = '<a href="'.DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1">'.$langs->trans("BackToModuleList").'</a>';
print load_fiche_titre($langs->trans("ChangeInvoiceRefSetup"), $linkback, 'title_setup');

print '<div class="info">';
print $langs->trans("ChangeInvoiceRefInfo");
print '</div>';

llxFooter();
$db->close();

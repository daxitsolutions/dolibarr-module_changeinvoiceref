<?php
if (!defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL', '1');
if (!defined('NOREQUIREMENU')) define('NOREQUIREMENU', '1');
if (!defined('NOREQUIREHTML')) define('NOREQUIREHTML', '1');
if (!defined('NOREQUIREAJAX')) define('NOREQUIREAJAX', '1');

$res = 0;
if (!$res && file_exists(dirname(__FILE__).'/../main.inc.php')) $res=@include dirname(__FILE__).'/../main.inc.php';
if (!$res && file_exists(dirname(__FILE__).'/../../../main.inc.php')) $res=@include dirname(__FILE__).'/../../../main.inc.php';
if (!$res) die('Include of main fails');

require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

header('Content-Type: application/json');

if (empty($user->rights->changeinvoiceref->change)) { echo json_encode(array('success'=>false,'error'=>'PERMISSION_DENIED')); exit; }

$invoice_id = (int) GETPOST('invoice_id', 'int');
$new_ref = GETPOST('new_ref', 'alpha');
$token = GETPOST('token', 'alpha');
$force_rename_with_documents = (int) GETPOST('force_rename_with_documents', 'int');

if (empty($token) || empty($_SESSION['newtoken']) || $token !== $_SESSION['newtoken']) { echo json_encode(array('success'=>false,'error'=>'BAD_TOKEN')); exit; }
if ($invoice_id<=0 || empty($new_ref)) { echo json_encode(array('success'=>false,'error'=>'BAD_PARAMS')); exit; }

$langs->load('changeinvoiceref@changeinvoiceref');

/**
 * Count generated invoice documents stored in the invoice directory.
 *
 * @param Conf    $conf    Global conf object
 * @param Facture $invoice Invoice object
 * @return array{count:int,path:string}
 */
function changeInvoiceRefCountDocuments($conf, $invoice)
{
    $invoice_output_dir = '';
    if (!empty($conf->facture->multidir_output[$invoice->entity])) {
        $invoice_output_dir = $conf->facture->multidir_output[$invoice->entity];
    } elseif (!empty($conf->facture->dir_output)) {
        $invoice_output_dir = $conf->facture->dir_output;
    } else {
        $invoice_output_dir = DOL_DATA_ROOT.'/facture';
    }

    $invoice_ref_dir = $invoice_output_dir.'/'.dol_sanitizeFileName($invoice->ref);
    if (!is_dir($invoice_ref_dir)) {
        return array('count' => 0, 'path' => $invoice_ref_dir);
    }

    $filearray = dol_dir_list($invoice_ref_dir, 'files', 1, '', array('(\.meta|_preview\.png)$', '^temp$', '^thumbs$', '^CVS$'));

    return array(
        'count' => is_array($filearray) ? count($filearray) : 0,
        'path' => $invoice_ref_dir
    );
}

$invoice = new Facture($db);
if ($invoice->fetch($invoice_id) <= 0) { echo json_encode(array('success'=>false,'error'=>'NOT_FOUND')); exit; }
if ((int) $invoice->statut !== (int) Facture::STATUS_DRAFT) { echo json_encode(array('success'=>false,'error'=>'NOT_DRAFT')); exit; }

$new_ref = dol_string_nospecial(trim($new_ref));
if (empty($new_ref)) { echo json_encode(array('success'=>false,'error'=>'INVALID_REF')); exit; }
if ($invoice->ref === $new_ref) { echo json_encode(array('success'=>true)); exit; }

$sql = "SELECT COUNT(*) as cnt FROM ".MAIN_DB_PREFIX."facture WHERE ref='".$db->escape($new_ref)."' AND entity IN (".getEntity('invoice').")";
$resql = $db->query($sql);
if ($resql) {
    $obj = $db->fetch_object($resql);
    if ($obj && (int)$obj->cnt > 0) { echo json_encode(array('success'=>false,'error'=>'REF_ALREADY_EXISTS')); exit; }
}

$document_info = changeInvoiceRefCountDocuments($conf, $invoice);
if ($document_info['count'] > 0 && !$force_rename_with_documents) {
    echo json_encode(array(
        'success' => false,
        'requires_confirmation' => true,
        'warning_title' => $langs->trans('ChangeInvoiceRefDocumentsWarningTitle'),
        'message' => $langs->trans('ChangeInvoiceRefDocumentsWarningMessage', $document_info['count'], $invoice->ref)
    ));
    exit;
}

$db->begin();
$res = $db->query("UPDATE ".MAIN_DB_PREFIX."facture SET ref='".$db->escape($new_ref)."' WHERE rowid=".(int)$invoice_id);
if (!$res) { $db->rollback(); echo json_encode(array('success'=>false,'error'=>'DB_FAIL')); exit; }
$db->commit();

echo json_encode(array('success'=>true));

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

header('Content-Type: application/json');

if (empty($user->rights->changeinvoiceref->change)) { echo json_encode(array('success'=>false,'error'=>'PERMISSION_DENIED')); exit; }

$invoice_id = (int) GETPOST('invoice_id', 'int');
$new_ref = GETPOST('new_ref', 'alpha');
$token = GETPOST('token', 'alpha');

if (empty($token) || empty($_SESSION['newtoken']) || $token !== $_SESSION['newtoken']) { echo json_encode(array('success'=>false,'error'=>'BAD_TOKEN')); exit; }
if ($invoice_id<=0 || empty($new_ref)) { echo json_encode(array('success'=>false,'error'=>'BAD_PARAMS')); exit; }

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

$db->begin();
$res = $db->query("UPDATE ".MAIN_DB_PREFIX."facture SET ref='".$db->escape($new_ref)."' WHERE rowid=".(int)$invoice_id);
if (!$res) { $db->rollback(); echo json_encode(array('success'=>false,'error'=>'DB_FAIL')); exit; }
$db->commit();

echo json_encode(array('success'=>true));

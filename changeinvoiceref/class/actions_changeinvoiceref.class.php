<?php
if (!defined('NOREQUIRESOC')) define('NOREQUIRESOC', '1');


class ActionsChangeInvoiceRef
{
    public function addMoreActionsButtons($parameters, &$object, &$action, $hookmanager)
    {
        global $langs, $user, $conf, $db;
        if (empty($object)) return 0;
        
        if ($object->element === 'facture') {
            if ((int) $object->statut !== 0) return 0;
            if (empty($user->rights->changeinvoiceref->change)) return 0;
            $langs->load('changeinvoiceref@changeinvoiceref');
            $token = newToken();
            $url = dol_buildpath('/changeinvoiceref/ajax/change_ref.php', 1);
            $warningTitle = json_encode($langs->transnoentities('ChangeInvoiceRefDocumentsWarningTitle'));
            $cancelAndDeleteLabel = json_encode($langs->transnoentities('ChangeInvoiceRefCancelAndDeleteDocs'));
            $forceRenameLabel = json_encode($langs->transnoentities('ChangeInvoiceRefForceRename'));
            $cancelledMessage = json_encode($langs->transnoentities('ChangeInvoiceRefCancelled'));
            print '<a class="butAction" id="btn_change_ref">'.$langs->trans('ChangeInvoiceRef').'</a>';
            print '<div id="dlg_change_ref" style="display:none">';
            print '<form id="form_change_ref">';
            print '<input type="hidden" name="token" value="'.$token.'">';
            print '<input type="hidden" name="invoice_id" value="'.((int)$object->id).'">';
            print '<label>'.$langs->trans('NewInvoiceRef').'</label><br>';
            print '<input type="text" name="new_ref" value="'.dol_escape_htmltag($object->ref).'" style="width:100%;margin-top:4px">';
            print '<div style="margin-top:8px"><button type="submit" class="button">'.$langs->trans('Change').'</button> <button type="button" class="button" id="btn_cancel_ref">'.$langs->trans('Cancel').'</button></div>';
            print '</form>';
            print '</div>';
            print '<div id="dlg_change_ref_warning" style="display:none"></div>';
            print '<script type="text/javascript">
jQuery(function($){
 var warningTitle = '.$warningTitle.';
 var cancelAndDeleteLabel = '.$cancelAndDeleteLabel.';
 var forceRenameLabel = '.$forceRenameLabel.';
 var cancelledMessage = '.$cancelledMessage.';

 function showWarningDialog(message){
  var html = $("<div/>").text(message || warningTitle).html().replace(/\\n/g,"<br>");
  $("#dlg_change_ref_warning").html("<p>"+html+"</p>").dialog({
   modal:true,
   title:warningTitle,
   width:560,
   buttons:[
    {
     text: cancelAndDeleteLabel,
     click:function(){
      $(this).dialog("close");
      $("#dlg_change_ref").dialog("close");
      alert(cancelledMessage);
     }
    },
    {
     text: forceRenameLabel,
     click:function(){
      $(this).dialog("close");
      submitChangeRef(true);
     }
    }
   ]
  });
 }

 function submitChangeRef(forceRename){
  var data = $("#form_change_ref").serializeArray();
  data.push({name:"force_rename_with_documents",value:forceRename ? "1" : "0"});
  $.post("'.$url.'",data,function(r){
   if(r&&r.success){
    location.reload();
   } else if(r&&r.requires_confirmation){
    showWarningDialog(r.message);
   } else {
    alert((r&&r.error)?r.error:"Error");
   }
  },"json").fail(function(){ alert("Error"); });
 }

 $("#btn_change_ref").on("click",function(){ $("#dlg_change_ref").dialog({modal:true,title:"'.$langs->transnoentities('ChangeInvoiceRef').'",width:480}); });
 $("#btn_cancel_ref").on("click",function(){ $("#dlg_change_ref").dialog("close"); });
 $("#form_change_ref").on("submit",function(e){ e.preventDefault(); submitChangeRef(false); });
});
</script>';
            return 0;
        }
        return 0;
    }
}

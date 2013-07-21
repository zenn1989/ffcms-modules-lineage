<h3>{$lang::com_lineage_donate_title} - {$lineage_server_name}</h3>
<hr />
<p>{$lang::com_lineage_donate_desc}</p>
<form class="form-horizontal" method="post">
<fieldset>
    <div class="control-group">
        <label class="control-label">{$lang::com_lineage_donate_label_count_title}</label>
        <div class="controls">
            <input type="text" class="input-large" name="l2_donate_count" placeholder="10"/>
            <p class="help-block">{$lang::com_lineage_donate_label_count_desc}</p>
        </div>
     </div>
    <div class="control-group">
        <label class="control-label">{$lang::com_lineage_donate_label_charname_title}</label>
        <div class="controls">
            <input type="text" class="input-large" name="l2_charname" placeholder="nagibator"/>
            <p class="help-block">{$lang::com_lineage_donate_label_charname_desc} {$lineage_server_name}</p>
        </div>
    </div>
    <div class="control-group">
        <div class="controls">
            <input type="submit" name="submit" class="btn btn-success" value="{$lang::com_lineage_donate_button_generate}" />
        </div>
    </div>
</fieldset>
</form>
{$if com.lineage.wp_payment_generated}
<p class="alert alert-info">{$lang::com_lineage_donate_gen_alert}</p>
<div class="row">
    <div class="span2"> &nbsp;</div>
    <div class="span5">
        <p>{$lang::com_lineage_donate_gen_id}: <strong>№{$lineage_trans_id}</strong></p>
        <p>{$lang::com_lineage_donate_gen_sum}: <strong>{$lineage_trans_price}{$lang::com_lineage_donate_gen_valute}</strong></p>
        <p>{$lang::com_lineage_donate_gen_char}: <strong>{$lineage_char_name}</strong></p>
        <form action='https://waytopay.org/merchant/index' method='post'>
            <input type="hidden" name="MerchantId" value="{$lineage_wp_id}">
            <input type="hidden" name="OutSum" value="{$lineage_trans_price}">
            <input type="hidden" name="InvId" value="{$lineage_trans_id}">
            <input type="hidden" name="InvDesc" value="Balance payment from {$lineage_char_name} on project {$url}">
            <input type="hidden" name="IncCurr" value="1">
            <input type="submit" value="Начать оплату счета" class="btn btn-danger">
        </form>
    </div>
</div>
{$/if}
{$notify}
<h3>{$lang::com_lineage_ma_change_title} - {$lineage_server_name}</h3>
<hr />
<p>{$lang::com_lineage_ma_change_desc} {$lineage_account_name}</p>
{$notify}
<form class="form-horizontal" method="post" action="">
    <div class="control-group">
        <label class="control-label">{$lang::com_lineage_ma_change_label_login}</label>
        <div class="controls">
            <input name="login" type="text" autocomplete="off" value="{$lineage_account_name}" disabled>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label">{$lang::com_lineage_ma_change_label_newpwd}</label>
        <div class="controls">
            <input name="password" type="password" autocomplete="off" required>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label">{$lang::com_lineage_ma_change_label_repwd}</label>
        <div class="controls">
            <input name="repassword" type="password" autocomplete="off" required>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label">{$lang::com_lineage_ma_change_label_oldpwd}</label>
        <div class="controls">
            <input name="oldpassword" type="password" autocomplete="off" required>
        </div>
    </div>

    <div class="control-group">
        <div class="controls">
            <input type="submit" name="password_change" value="{$lang::com_lineage_ma_change_button_pass}" class="btn btn-success" />
        </div>
    </div>

</form>
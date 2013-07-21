<h3>{$lang::com_lineage_reg_title} - {$lineage_server_name}</h3>
<hr />
<p>{$lang::com_lineage_reg_desc} - {$lineage_server_name}</p>
{$if user.auth}
    <p class="alert alert-success">{$lang::com_lineage_reg_auth}</p>
{$/if}
{$if !user.auth}
<p class="alert alert-error">{$lang::com_lineage_reg_guest}</p>
{$/if}
{$notify}
<form class="form-horizontal" method="post" action="">
    <div class="control-group">
        <label class="control-label">{$lang::com_lineage_reg_label_login}</label>
        <div class="controls">
            <input name="login" type="text" placeholder="ivan2013" autocomplete="off" required>
            <p class="help-block">{$lang::com_lineage_reg_help_login}</p>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label">{$lang::com_lineage_reg_label_pass}</label>
        <div class="controls">
            <input name="pass" type="password" placeholder="4Strong1Password" autocomplete="off" required>
            <p class="help-block">{$lang::com_lineage_reg_help_pass}</p>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label">{$lang::com_lineage_reg_label_repass}</label>
        <div class="controls">
            <input name="repass" type="password" placeholder="4Strong1Password" autocomplete="off" required>
            <p class="help-block">{$lang::com_lineage_reg_help_repass}</p>
        </div>
    </div>
    {$if !user.auth}
    <div class="control-group">
        <label class="control-label">{$lang::com_lineage_reg_label_mail}</label>
        <div class="controls">
            <input name="email" type="text" placeholder="stalone@mail.ru" autocomplete="off" required>
            <p class="help-block">{$lang::com_lineage_reg_help_mail}</p>
        </div>
    </div>
    {$/if}
    <div class="control-group">
        <div class="controls">
            <input type="submit" name="submit" class="btn btn-inverse" value="{$lang::com_lineage_reg_button}"/>
        </div>
    </div>
</form>
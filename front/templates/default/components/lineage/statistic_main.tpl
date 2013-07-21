<h3>{$lang::com_lineage_stats_main_title} - {$lineage_server_name}</h3>
<hr />
<p>{$lang::com_lineage_stats_main_desc}</p>
<div class="row">
    <div class="span4">
        <table class="table table-bordered">
            <tr>
                <td>{$lang::com_lineage_stats_main_login}</td>
                <td>{$lineage_server_login_status}</td>
            </tr>
            <tr>
                <td>{$lang::com_lineage_stats_main_game}</td>
                <td>{$lineage_server_game_status}</td>
            </tr>
            <tr>
                <td>{$lang::com_lineage_stats_main_online}</td>
                <td>{$lineage_server_online}</td>
            </tr>
            {$if com.lineage.show_accounts}
            <tr>
                <td>{$lang::com_lineage_stats_main_accounts}</td>
                <td>{$lineage_server_accounts}</td>
            </tr>
            {$/if}
            {$if com.lineage.show_characters}
            <tr>
                <td>{$lang::com_lineage_stats_main_characters}</td>
                <td>{$lineage_server_characters}</td>
            </tr>
            {$/if}
        </table>
    </div>
    <div class="span4">
        <ul class="nav nav-tabs nav-stacked">
            {$if com.lineage.show_pvptop}
            <li><a href="{$url}/lineage/{$lineage_server_id}/stats/pvp.html">{$lang::com_lineage_stats_main_pvpraiting}</a></li>
            {$/if}
            {$if com.lineage.show_pktop}
            <li><a href="{$url}/lineage/{$lineage_server_id}/stats/pk.html">{$lang::com_lineage_stats_main_pkraiting}</a></li>
            {$/if}
            {$if com.lineage.show_clantop}
            <li><a href="{$url}/lineage/{$lineage_server_id}/stats/clan.html">{$lang::com_lineage_stats_main_clanraiting}</a></li>
            {$/if}
        </ul>
    </div>
</div>
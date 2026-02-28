{if isset($idDataTable.id)}
    {assign var="idDataTable" value=$idDataTable.id}
{/if}
{if !$returnAsScript}
    <script type="text/javascript">
        var oTable{$idDataTable|escape:'htmlall':'UTF-8'} = undefined;
        {literal}
        $(document).ready(function() {
        {/literal}
{/if}
            oTable{$idDataTable|escape:'htmlall':'UTF-8'} = $('#{$idDataTable|escape:'htmlall':'UTF-8'}').dataTable({
                "sDom": 'R<"H"lfr>t<"F"ip<',
                "bJQueryUI": true,
                "bStateSave": true,
                "sPaginationType": "full_numbers",
                "bDestory": true,
                "oLanguage": {
                    "sLengthMenu": "{l s='Display' mod='pm_seointernallinking'} _MENU_ {l s='records per page' mod='pm_seointernallinking'}",
                    "sZeroRecords": "{l s='Nothing found - sorry' mod='pm_seointernallinking'}",
                    "sInfo": "{l s='Showing' mod='pm_seointernallinking'} _START_ {l s='to' mod='pm_seointernallinking'} _END_ {l s='of' mod='pm_seointernallinking'} _TOTAL_ {l s='records' mod='pm_seointernallinking'}",
                    "sInfoEmpty": "{l s='Showing' mod='pm_seointernallinking'} 0 {l s='to' mod='pm_seointernallinking'} 0 {l s='of' mod='pm_seointernallinking'} 0 {l s='records' mod='pm_seointernallinking'}",
                    "sInfoFiltered": "({l s='filtered from' mod='pm_seointernallinking'} _MAX_ {l s='total records' mod='pm_seointernallinking'})",
                    "sPageNext": "{l s='Next' mod='pm_seointernallinking'}",
                    "sPagePrevious": "{l s='Previous' mod='pm_seointernallinking'}",
                    "sPageLast": "{l s='Last' mod='pm_seointernallinking'}",
                    "sPageFirst": "{l s='First' mod='pm_seointernallinking'}",
                    "sSearch": "{l s='Search' mod='pm_seointernallinking'}",
                    oPaginate: {
                        "sFirst":"{l s='First' mod='pm_seointernallinking'}",
                        "sPrevious": "{l s='Previous' mod='pm_seointernallinking'}",
                        "sNext": "{l s='Next' mod='pm_seointernallinking'}",
                        "sLast": "{l s='Last' mod='pm_seointernallinking'}"
{literal}
                    }
                }
            });
{/literal}
{if !$returnAsScript}
        {literal}
        });
        {/literal}
    </script>
{/if}
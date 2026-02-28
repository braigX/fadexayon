$('#img{$elementID}').attr('src', '{$state.path}').attr('alt', '{$state.label}').attr('title', '{$state.label}');
$('#span{$elementID}').parents('td').children("span").html("{$state.value}");
show_info("{l s='Saved' mod='pm_seointernallinking'}");
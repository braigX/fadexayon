{sil_startForm id="formGlobalOptions" iframetarget=false target='dialogIframePostForm'}

{sil_inputActive obj=$obj key_active='_exclude_headings' key_db='_exclude_headings' label={l s='Do not add links in headings (h1, h2, etc.)' mod='pm_seointernallinking'} defaultvalue=$default_config.exclude_headings}

{sil_select obj=$obj key='_description_field' options=$options.description_field label={l s='Description field to be used (for the products)' mod='pm_seointernallinking'} defaultvalue=false size='200px'}

{sil_select obj=$obj key='_default_datatables_length' options=$options.default_datatables_length label={l s='Number of lines to be displayed in the tables' mod='pm_seointernallinking'} defaultvalue=false size='100px'}

{module->_displaySubmit text="{l s='Save' mod='pm_seointernallinking'}" name='submit_global_options'}

{sil_endForm id="formGlobalOptions" includehtmlatend=true}
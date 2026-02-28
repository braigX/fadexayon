<div id="task_form" style="display:none;">
        <div class="panel taskMetaproduct">
            <div class="task-grp">
                <label class="control-label task-label">
                    {l s='Bulk meta Products' mod='ec_seo'}
                </label>
                <button class="btn btn-default task-btn" onclick="javascript:$.post('{$bulkMetaProducts|escape:'htmlall':'UTF-8'}');showNoticeMessage('{$trad_tasklaunched|escape:'htmlall':'UTF-8'}');return false;">
                    {l s='Launch' mod='ec_seo'}
                </button>
                <div class="margin-form task-form">
                    <input type="text" readonly value="{$bulkMetaProducts|escape:'htmlall':'UTF-8'}" style="cursor: initial;"/>
                </div>
            </div>
            <div class="ec_suivi_task refreshMetaProductsPanel" >
                <i>{l s='(leave the mouse in the frame for a real-time display update)' mod='ec_seo'}</i>
                <div class="tabrefreshmetaProductspanel"></div>
            </div>
        </div>
        

        <div class="panel taskMetacategory">
            <div class="task-grp">
                <label class="control-label task-label">
                    {l s='Bulk meta Categories' mod='ec_seo'}
                </label>
                <button class="btn btn-default task-btn" onclick="javascript:$.post('{$bulkMetaCategories|escape:'htmlall':'UTF-8'}');showNoticeMessage('{$trad_tasklaunched|escape:'htmlall':'UTF-8'}');return false;">
                    {l s='Launch' mod='ec_seo'}
                </button>
                <div class="margin-form task-form">
                    <input type="text" readonly value="{$bulkMetaCategories|escape:'htmlall':'UTF-8'}" style="cursor: initial;"/>
                </div>
            </div>
            <div class="ec_suivi_task refreshMetaCategoriesPanel" >
                <i>{l s='(leave the mouse in the frame for a real-time display update)' mod='ec_seo'}</i>
                <div class="tabrefreshmetaCategoriespanel"></div>
            </div>
        </div>
        

        <div class="panel taskMetacms">
            <div class="task-grp">
                <label class="control-label task-label">
                    {l s='Bulk meta CMS' mod='ec_seo'}
                </label>
                <button class="btn btn-default task-btn" onclick="javascript:$.post('{$bulkMetaCMS|escape:'htmlall':'UTF-8'}');showNoticeMessage('{$trad_tasklaunched|escape:'htmlall':'UTF-8'}');return false;">
                    {l s='Launch' mod='ec_seo'}
                </button>
                <div class="margin-form task-form">
                    <input type="text" readonly value="{$bulkMetaCMS|escape:'htmlall':'UTF-8'}" style="cursor: initial;"/>
                </div>
            </div>
            <div class="ec_suivi_task refreshMetaCMSPanel" >
                <i>{l s='(leave the mouse in the frame for a real-time display update)' mod='ec_seo'}</i>
                <div class="tabrefreshmetaCMSpanel"></div>
            </div>
        </div>
        

        <div class="panel taskMetasupplier">
            <div class="task-grp">
                <label class="control-label task-label">
                    {l s='Bulk meta Suppliers' mod='ec_seo'}
                </label>
                <button class="btn btn-default task-btn" onclick="javascript:$.post('{$bulkMetaSuppliers|escape:'htmlall':'UTF-8'}');showNoticeMessage('{$trad_tasklaunched|escape:'htmlall':'UTF-8'}');return false;">
                    {l s='Launch' mod='ec_seo'}
                </button>
                <div class="margin-form task-form">
                    <input type="text" readonly value="{$bulkMetaSuppliers|escape:'htmlall':'UTF-8'}" style="cursor: initial;"/>
                </div>
            </div>
            <div class="ec_suivi_task refreshMetaSuppliersPanel" >
                <i>{l s='(leave the mouse in the frame for a real-time display update)' mod='ec_seo'}</i>
                <div class="tabrefreshmetaSupplierspanel"></div>
            </div>
        </div>
        

        <div class="panel taskMetamanufacturer">
            <div class="task-grp">
                <label class="control-label task-label">
                    {l s='Bulk meta Manufacturers' mod='ec_seo'}
                </label>
                <button class="btn btn-default task-btn" onclick="javascript:$.post('{$bulkMetaManufacturers|escape:'htmlall':'UTF-8'}');showNoticeMessage('{$trad_tasklaunched|escape:'htmlall':'UTF-8'}');return false;">
                    {l s='Launch' mod='ec_seo'}
                </button>
                <div class="margin-form task-form">
                    <input type="text" readonly value="{$bulkMetaManufacturers|escape:'htmlall':'UTF-8'}" style="cursor: initial;"/>
                </div>
            </div>
            <div class="ec_suivi_task refreshMetaManufacturersPanel" >
                <i>{l s='(leave the mouse in the frame for a real-time display update)' mod='ec_seo'}</i>
                <div class="tabrefreshmetaManufacturerspanel"></div>
            </div>
        </div>
        

        <div class="panel taskImproduct">
            <div class="task-grp">
                <label class="control-label task-label">
                    {l s='Internal mesh Products' mod='ec_seo'}
                </label>
                <button class="btn btn-default task-btn" onclick="javascript:$.post('{$bulkImProducts|escape:'htmlall':'UTF-8'}');showNoticeMessage('{$trad_tasklaunched|escape:'htmlall':'UTF-8'}');return false;">
                    {l s='Launch' mod='ec_seo'}
                </button>
                <div class="margin-form task-form">
                    <input type="text" readonly value="{$bulkImProducts|escape:'htmlall':'UTF-8'}" style="cursor: initial;"/>
                </div>
            </div>
            <div class="ec_suivi_task refreshImProductsPanel" >
                <i>{l s='(leave the mouse in the frame for a real-time display update)' mod='ec_seo'}</i>
                <div class="tabrefreshImProductspanel"></div>
            </div>
        </div>
        

        <div class="panel taskImcategory">
            <div class="task-grp">
                <label class="control-label task-label">
                    {l s='Internal mesh Categories' mod='ec_seo'}
                </label>
                <button class="btn btn-default task-btn" onclick="javascript:$.post('{$bulkImCategories|escape:'htmlall':'UTF-8'}');showNoticeMessage('{$trad_tasklaunched|escape:'htmlall':'UTF-8'}');return false;">
                    {l s='Launch' mod='ec_seo'}
                </button>
                <div class="margin-form task-form">
                    <input type="text" readonly value="{$bulkImCategories|escape:'htmlall':'UTF-8'}" style="cursor: initial;"/>
                </div>
            </div>
            <div class="ec_suivi_task refreshImCategoriesPanel" >
                <i>{l s='(leave the mouse in the frame for a real-time display update)' mod='ec_seo'}</i>
                <div class="tabrefreshImCategoriespanel"></div>
            </div>
        </div>
        

        <div class="panel taskImcms">
            <div class="task-grp">
                <label class="control-label task-label">
                    {l s='Internal mesh CMS' mod='ec_seo'}
                </label>
                <button class="btn btn-default task-btn" onclick="javascript:$.post('{$bulkImCMS|escape:'htmlall':'UTF-8'}');showNoticeMessage('{$trad_tasklaunched|escape:'htmlall':'UTF-8'}');return false;">
                    {l s='Launch' mod='ec_seo'}
                </button>
                <div class="margin-form task-form">
                    <input type="text" readonly value="{$bulkImCMS|escape:'htmlall':'UTF-8'}" style="cursor: initial;"/>
                </div>
            </div>
            <div class="ec_suivi_task refreshImCMSPanel" >
                <i>{l s='(leave the mouse in the frame for a real-time display update)' mod='ec_seo'}</i>
                <div class="tabrefreshImCMSpanel"></div>
            </div>
        </div>
        

        <div class="panel taskImsupplier">
            <div class="task-grp">
                <label class="control-label task-label">
                    {l s='Internal mesh Suppliers' mod='ec_seo'}
                </label>
                <button class="btn btn-default task-btn" onclick="javascript:$.post('{$bulkImSuppliers|escape:'htmlall':'UTF-8'}');showNoticeMessage('{$trad_tasklaunched|escape:'htmlall':'UTF-8'}');return false;">
                    {l s='Launch' mod='ec_seo'}
                </button>
                <div class="margin-form task-form">
                    <input type="text" readonly value="{$bulkImSuppliers|escape:'htmlall':'UTF-8'}" style="cursor: initial;"/>
                </div>
            </div>
            <div class="ec_suivi_task refreshImSuppliersPanel" >
                <i>{l s='(leave the mouse in the frame for a real-time display update)' mod='ec_seo'}</i>
                <div class="tabrefreshImSupplierspanel"></div>
            </div>
        </div>
        

        <div class="panel taskImmanufacturer">
            <div class="task-grp">
                <label class="control-label task-label">
                    {l s='Internal mesh Manufacturers' mod='ec_seo'}
                </label>
                <button class="btn btn-default task-btn" onclick="javascript:$.post('{$bulkImManufacturers|escape:'htmlall':'UTF-8'}');showNoticeMessage('{$trad_tasklaunched|escape:'htmlall':'UTF-8'}');return false;">
                    {l s='Launch' mod='ec_seo'}
                </button>
                <div class="margin-form task-form">
                    <input type="text" readonly value="{$bulkImManufacturers|escape:'htmlall':'UTF-8'}" style="cursor: initial;"/>
                </div>
            </div>
            <div class="ec_suivi_task refreshImManufacturersPanel" >
                <i>{l s='(leave the mouse in the frame for a real-time display update)' mod='ec_seo'}</i>
                <div class="tabrefreshImManufacturerspanel"></div>
            </div>
        </div>
        


    
</div>

{**
* 2007-2020 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2020 Innova Deluxe SL
* @license   INNOVADELUXE
*}

<div class="modal fade" id="constraints_modal" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">
                    {l s='Options over component' mod='idxrcustomproduct'} <span class='modal_component_name'></span>
                </h4>
            </div>
            <div class="modal-body">
                <div id="constraints_modal_default_value">
                    <input type="hidden" id="constraint_component_id" data-id="">
                    <input type="hidden" id="constraint_configuration_id" data-id="">
                    <h2>{l s='Default value' mod='idxrcustomproduct'}</h2>
                    <hr/>
                    <div class="form-group">
                        <label for="default_option">{l s='Select a default value for this component in this configuration' mod='idxrcustomproduct'}</label>
                        <select class="form-control" id="default_options">
                            <option value="disable" class="fixed_option" >{l s='Do not use default value' mod='idxrcustomproduct'}</option>
                            <option value="inherit" class="fixed_option" selected>{l s='Inherited from the component' mod='idxrcustomproduct'}</option>
                        </select>
                    </div>
                </div>
                <div id="constraints_modal_constraints">
                    <h2>{l s='Actual constraints' mod='idxrcustomproduct'}</h2><hr/>
                    <table id="constraint_list" class="table table-striped"></table><br/>
                    <div class="form-group">
                        <label for="constraint_options">{l s='Show this component if the option is marked' mod='idxrcustomproduct'}</label>
                        <select class="form-control" id="constraint_options">
                            <option>{l s='There are no options' mod='idxrcustomproduct'}</option>
                        </select>
                    </div>                    
                    <button type="button" class="btn btn-primary" id="send_constrain">{l s='Add' mod='idxrcustomproduct'}</button>
                </div>
                <div id="constraints_modal_impact">
                    <h2>{l s='Aditional price impacts' mod='idxrcustomproduct'}</h2><hr/>
                    <table id="impact_list" class="table table-striped"></table><br/>
                    <div class="form-group">
                        <label for="impacttrigger_options">{l s='Trigger option (option that originates the increment)' mod='idxrcustomproduct'}</label>
                        <select class="form-control" id="impacttrigger_options">
                            <option>{l s='There are no options' mod='idxrcustomproduct'}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="impacttarget_options">{l s='Target option' mod='idxrcustomproduct'}</label>
                        <select class="form-control" id="impacttarget_options">
                            <option>{l s='There are no options' mod='idxrcustomproduct'}</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="impactoption_percent">{l s='Percent' mod='idxrcustomproduct'}</label>            
                            <input type="number" step="0.01" class="form-control" id="impactoption_percent" placeholder="{l s='Percent' mod='idxrcustomproduct'}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="impactoption_fixed">{l s='Fixed (with taxes)' mod='idxrcustomproduct'}</label>
                            <input type="number" step="0.01" class="form-control" id="impactoption_fixed" placeholder="{l s='Fixed' mod='idxrcustomproduct'}">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary" id="send_impact">{l s='Add' mod='idxrcustomproduct'}</button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{l s='Close' mod='idxrcustomproduct'}</button>
            </div>
        </div>

    </div>
</div>
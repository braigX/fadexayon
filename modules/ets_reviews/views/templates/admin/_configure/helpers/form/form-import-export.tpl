{*
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
*}
{if $input.name == 'ETS_RV_IE_DATA_IMPORT'}
<div class="ets_rv_export_form">
	<div class="ets_rv_export">
		<div class="ets_rv_export_form_content from-group">
			<div class="ets_rv_export_option{if $productComment} col-lg-4{else} col-lg-6{/if}">
				<div class="export_title">{l s='Export product review data' mod='ets_reviews'}</div>
				<a class="btn btn-default ets_rv_export_data" href="{$currentIndex|cat:'&action=generateArchive' nofilter}" target="_blank">
					<i class="icon-download"></i>&nbsp;{l s='Export reviews' mod='ets_reviews'}
				</a>
				<p class="ets_rv_export_option_note">{l s='Export all product review data including all settings, reviews, questions and answers, review criteria, etc.' mod='ets_reviews'}</p>
				<label for="ETS_RV_IE_EXPORT_PHOTOS">
					<input id="ETS_RV_IE_EXPORT_PHOTOS" type="checkbox" name="ETS_RV_IE_EXPORT_PHOTOS" checked="checked"> {l s='Export review photos' mod='ets_reviews'}
				</label>
			</div>
			<div class="ets_rv_import_option{if $productComment} col-lg-4{else} col-lg-6{/if}">
				<div class="export_title">{l s='Import product review data' mod='ets_reviews'}</div>
				<div class="ets_rv_import_option_form">
					<div class="form-group row ets_rv_import_option_data">
						<label for="data" class="col-lg-3">{l s='Product review data package' mod='ets_reviews'}</label>
                        <div class="col-lg-9">
						  <input id="data" name="data" type="file" data-url="{$currentIndex|cat:'&ajax=1&action=uploadData' nofilter}" data-error="{l s='Package is empty! Please upload a valid data package before importing' mod='ets_reviews'}"/>
					    </div>
                    </div>
					<div class="ets_rv_import_option_clean">
						<span>{l s='All product review configurations and product review photos of the current shop will be deleted when you start the import. We recommend you export product review data of the current shop and save it as a backup (just by clicking on the "Export reviews" button) before importing new data. ' mod='ets_reviews'}</span>
					</div>
                    {/if}
                    {if $input.name == 'ETS_RV_IE_DELETE_ALL'}
                    <div class="form-group row">
                        <label class="control-label col-lg-3 xs-hide">&nbsp;</label>
                        <div class="col-lg-9">
        					<div class="ets_rv_import_option_button">
        						<div class="ets_rv_import_data_loading"><img src="#"/>{l s='Importing data' mod='ets_reviews'}</div>
        						<div class="ets_rv_import_data_submit">
        							<a href="{$currentIndex|cat:'&ajax=1&action=importData' nofilter}" class="btn btn-default ets_rv_import_data" data-empty="{l s='No import data types available' mod='ets_reviews'}">
        								<i class="icon-compress"></i> {l s='Import reviews' mod='ets_reviews'}
        							</a>
        						</div>
        					</div>
                        </div>
                    </div>
				</div>
                <div class="export_title">{l s='Import product review data using csv or xlsx file' mod='ets_reviews'}</div>
                <div class="ets_rv_import_option_form">
                    <div class="form-group row ets_rv_import_option_data">
                        <label for="data" class="col-lg-3">{l s='Product review data package' mod='ets_reviews'}</label>
                        <div class="col-lg-9">
                            <input id="data_csv_or_xlsx" name="data_csv_or_xlsx" type="file" />
                            <p class="help-block">
                                {l s='Accepted formats: .csv, .xlsx. Example file: ' mod='ets_reviews'}<a href="{$currentIndex nofilter}&action=downloadFileExample">Import_reviews_example.xlsx</a>
                            </p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-lg-3">{l s='Remove all review data before importing' mod='ets_reviews'}</label>
                        <div class="col-lg-9">
                            <span class="switch prestashop-switch fixed-width-lg">
                                <input type="radio" name="data_csv_or_xlsx_delete_all" id="data_csv_or_xlsx_delete_all_on" value="1">
                                <label for="data_csv_or_xlsx_delete_all_on">{l s='Yes' mod='ets_reviews'}</label>
                                <input type="radio" name="data_csv_or_xlsx_delete_all" id="data_csv_or_xlsx_delete_all_off" value="0" checked="checked">
                                <label for="data_csv_or_xlsx_delete_all_off">{l s='No' mod='ets_reviews'}</label><a class="slide-button btn"></a>
                            </span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-lg-3 xs-hide">&nbsp;</label>
                        <div class="col-lg-9">
                            <div class="ets_rv_import_option_button">
                                <div class="ets_rv_import_data_loading"><img src="#"/>{l s='Importing data' mod='ets_reviews'}</div>
                                <div class="ets_rv_import_data_submit">
                                    <a href="{$currentIndex|cat:'&ajax=1&action=importDataReviews' nofilter}" class="btn btn-default ets_rv_import_data_csv" data-empty="{l s='No import data types available' mod='ets_reviews'}">
                                        <i class="icon-compress"></i> {l s='Import reviews' mod='ets_reviews'}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="export_title">{l s='Import product question & answer data using csv or xlsx file' mod='ets_reviews'}</div>
                <div class="ets_rv_import_option_form">
                    <div class="form-group row ets_rv_import_option_data">
                        <label for="data" class="col-lg-3">{l s='Product question & answer data package' mod='ets_reviews'}</label>
                        <div class="col-lg-9">
                            <input id="data_qa_csv_or_xlsx" name="data_qa_csv_or_xlsx" type="file" />
                            <p class="help-block">
                                {l s='Accepted formats: .csv, .xlsx. Example file: ' mod='ets_reviews'}<a href="{$currentIndex nofilter}&action=downloadFileExampleQuestion">Import_questions_example.xlsx</a>
                            </p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-lg-3">{l s='Remove all question data before importing' mod='ets_reviews'}</label>
                        <div class="col-lg-9">
                            <span class="switch prestashop-switch fixed-width-lg">
                                <input type="radio" name="data_qa_csv_or_xlsx_delete_all" id="data_qa_csv_or_xlsx_delete_all_on" value="1">
                                <label for="data_qa_csv_or_xlsx_delete_all_on">{l s='Yes' mod='ets_reviews'}</label>
                                <input type="radio" name="data_qa_csv_or_xlsx_delete_all" id="data_qa_csv_or_xlsx_delete_all_off" value="0" checked="checked">
                                <label for="data_qa_csv_or_xlsx_delete_all_off">{l s='No' mod='ets_reviews'}</label><a class="slide-button btn"></a>
                            </span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-lg-3 xs-hide">&nbsp;</label>
                        <div class="col-lg-9">
                            <div class="ets_rv_import_option_button">
                                <div class="ets_rv_import_data_loading"><img src="#"/>{l s='Importing data' mod='ets_reviews'}</div>
                                <div class="ets_rv_import_data_submit">
                                    <a href="{$currentIndex|cat:'&ajax=1&action=importDataQuestions' nofilter}" class="btn btn-default ets_rv_import_data_qa" data-empty="{l s='No import data types available' mod='ets_reviews'}">
                                        <i class="icon-compress"></i> {l s='Import question & answer' mod='ets_reviews'}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
			</div>
            {if $productComment}
                <div class="ets_rv_import_option col-lg-4" id="import_productcomment_prestashop">
                    <div class="export_title">{l s='Import data from "Product Comment" module by PrestaShop' mod='ets_reviews'}</div>
				        <div class="ets_rv_import_option_form">
                            <div class="ets_rv_import_option_clean">
        						<span>{l s='Our module is also compatible with “Product Comments” – the native customer review module by PrestaShop. You can synchronize the data of “Product Comments” module with our “Product Reviews - Ratings, Google Snippets, Q&A” module to make sure the reviews are always up – to – date.' mod='ets_reviews'}</span>
        					</div>
                            <div class="form-group row">
                                <label class="control-label col-lg-3 xs-hide">{l s='Criterion mapping' mod='ets_reviews'}</label>
                                {if $old_criterions}
									<div class="col-lg-9 row">
                                    {foreach from=$old_criterions item='criterion'}
											<div class="criterion_comments">
												<div class="col-lg-5 criterion_old">
													{$criterion.name|escape:'html':'UTF-8'}
												</div>
												<span class="arrow_merge col-lg-2">
													<svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M979 960q0 13-10 23l-466 466q-10 10-23 10t-23-10l-50-50q-10-10-10-23t10-23l393-393-393-393q-10-10-10-23t10-23l50-50q10-10 23-10t23 10l466 466q10 10 10 23zm384 0q0 13-10 23l-466 466q-10 10-23 10t-23-10l-50-50q-10-10-10-23t10-23l393-393-393-393q-10-10-10-23t10-23l50-50q10-10 23-10t23 10l466 466q10 10 10 23z"/></svg>
												</span>
												<div class="col-lg-5 criterion_new">
													<select name="new_criterions[{$criterion.id_product_comment_criterion|intval}]">
														{assign var='selected' value=false}
														{if $new_criterions}
															{foreach from=$new_criterions item='new_criterion'}
																<option value="{$new_criterion.id_ets_rv_product_comment_criterion|intval}"{if $new_criterion.id_ets_rv_product_comment_criterion==$criterion.id_product_comment_criterion}{assign var='selected' value=true} selected="selected"{/if}>{$new_criterion.name|escape:'html':'UTF-8'}</option>
															{/foreach}
														{/if}
														<option value="0"{if !$selected} selected=""{/if}>{l s='Create new' mod='ets_reviews'}</option>
													</select>
												</div>
											</div>

                                    {/foreach}
									</div>
                                {/if}
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-lg-3 xs-hide">&nbsp;</label>
                                <div class="col-lg-9">
                					<div class="ets_rv_import_option_button">
                						<div class="ets_rv_import_data_loading"><img src="#"/>{l s='Importing data' mod='ets_reviews'}</div>
                						<div class="ets_rv_import_data_submit">
                							<a href="{$currentIndex|cat:'&ajax=1&action=importDataPrestashop' nofilter}" class="btn btn-default ets_rv_import_data prestashop" data-empty="{l s='No import data types available' mod='ets_reviews'}">
                								<i class="icon-compress"></i> {l s='Import' mod='ets_reviews'}
                							</a>
                						</div>
                					</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            {/if}
        <div class="clearfix"></div>
		</div>
	</div>
</div>
{/if}
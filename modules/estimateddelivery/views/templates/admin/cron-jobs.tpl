{** * Estimated Delivery - Front Office Feature
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2015
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @category Transport & Logistics
 * Registered Trademark & Property of smart-modules.com
 *
 * ***************************************************
 * *               Estimated Delivery                *
 * *          http://www.smart-modules.com           *
 * *                                                  *
 * ***************************************************
 *}

{if isset($message)}{$message|escape:'htmlall':'UTF-8'}{/if}
{if $old_ps == 1}
<fieldset id="cron_jobs">
    <legend><i class="icon-time"></i> {l s='Cron Job' mod='estimateddelivery'}</legend>
{else}
<div class="panel" id="cron_jobs">
    <div class="panel-heading"><i class="icon-time"></i> {l s='Cron Job' mod='estimateddelivery'}</div>
{/if}

    <!-- Instruction on how to create the cron job -->
    <div class="row">
        <div class="col-lg-12">
            <h2>{l s='Setting up the cron job through the server' mod='estimateddelivery'} - ({l s='Recommended' mod='estimateddelivery'})</h2>
            <div class="{if $old_ps}hint{else}alert alert-info{/if}">
                <p>{l s='There are many many server hosting management systems, but this guide will guide you step by step to ' mod='estimateddelivery'}</p>
                <p>{l s='To set up the Cron Job you need first to open the page where you manage your domain, FTP or email accounts...' mod='estimateddelivery'}<br>
                {l s='The two most common are cPanel or Plesk, but there are many more' mod='estimateddelivery'}.</p>
                <p>{l s='Once you have entered the management interface you should look for something like Cron Jobs or Scheduled Tasks' mod='estimateddelivery'}.<br>
                {l s='You almost have it! Now it\'s time to enter the schedule and the command' mod='estimateddelivery'}.</p>
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-lg-12">
            <div class="pull-right embed-responsive embed-responsive-4by3">
                <h3 class="modal-title text-info">{l s='Video guide - How to create a cron job' mod='estimateddelivery'}</h3>
                <iframe class="embed-responsive-item" width="560" height="420"  src="https://www.youtube.com/embed/KzySWKn1z8E" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
            <h3 class="modal-title text-info">{l s='What\'s a Cron Job?' mod='estimateddelivery'}</h3>
            <p><strong>{l s='A cron job is a task that repeats every X time' mod='estimateddelivery'}</strong>.</p>
            {l s='The cron job can be divided in three parts the schedule and the command and the output' mod='estimateddelivery'}</p>
            <span class="badge">{l s='Schedule' mod='estimateddelivery'}</span> + <span class="badge">{l s='Command' mod='estimateddelivery'}</span> + <span class="badge">{l s='Output' mod='estimateddelivery'}</span>
            <br><br>
            <p><u>{l s='For example, this Cron Job:' mod='estimateddelivery'}</u></p>
            <p><strong>50 * * * * curl -l -k "{$DD_CRON_URL|escape:'htmlall':'UTF-8'}" >/dev/null 2>&1</strong></p>
            <p>{l s='Will generate the feed at minute 50 of every hour for every day and month indefinitely' mod='estimateddelivery'} (0:50, 1:50, 2:50...)</p>
            <hr>
            <h3 class="modal-title text-info">{l s='Schedule' mod='estimateddelivery'} - {l s='How does the schedule work?' mod='estimateddelivery'}</h3>
            <p>{l s='The schedule tells the crob how often the command or script has to be executed.' mod='estimateddelivery'}</p>
            <p>{l s='In some systems, you will have to choose among prefedined options like once a day, every hour, every 3 hours...' mod='estimateddelivery'} {l s='but in most cases, you will be able to choose the programmation yourself. To set up the schedule you will find 5 parameters to configure' mod='estimateddelivery'}</p>
             +---------------- {l s='minute' mod='estimateddelivery'} (0 - 59)<br/>
             | &nbsp;+------------- {l s='hour' mod='estimateddelivery'} (0 - 23)<br/>
             | &nbsp;| &nbsp;+---------- {l s='day of month' mod='estimateddelivery'} (1 - 31)<br/>
             | &nbsp;| &nbsp;| &nbsp;+------- {l s='month' mod='estimateddelivery'} (1 - 12)<br/>
             | &nbsp;| &nbsp;| &nbsp;| &nbsp;+---- {l s='day of week' mod='estimateddelivery'} (0 - 6) ({l s='Sunday' mod='estimateddelivery'}=0 {l s='or' mod='estimateddelivery'} 7)<br/>
             | &nbsp;| &nbsp;| &nbsp;| &nbsp;|<br/>
             * &nbsp;* &nbsp;* &nbsp;* &nbsp;* &nbsp;</p>

            <p>{l s='In each field you can set up a number or an asterisk' mod='estimateddelivery'}.<br>
            {l s='The asterisk has the special meaning "every"' mod='estimateddelivery'} ({l s='"every hour", "every minute"...' mod='estimateddelivery'})<br>
            {l s='You can also modify the asterisk operator by adding a slash and a number after it to set an interval' mod='estimateddelivery'}.<br>
            {l s='To make it easier for you to understand. Here are the most common schedules to generate the feed:' mod='estimateddelivery'}
            <table class="table">
                <thead>
                    <tr>
                        <th>{l s='Schedule' mod='estimateddelivery'}</th>
                        <th>{l s='What it does?' mod='estimateddelivery'}</th>
                        <th>{l s='Will be executed at...' mod='estimateddelivery'}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>0 * * * *</td>
                        <td>{l s='Generate the feed each hour at minute 0' mod='estimateddelivery'}</td>
                        <td>(0:00, 1:00, 2:00...)</td>
                    </tr>
                    <tr>
                        <td>0 */3 * * *</td>
                        <td>{l s='Generate the feed every %s hours at minute 0' mod='estimateddelivery' sprintf=[3]}</td>
                        <td>(0:00, 3:00, 6:00...)</td>
                    </tr>
                    <tr>
                        <td>0 */6 * * *</td>
                        <td>{l s='Generate the feed every %s hours at minute 0' mod='estimateddelivery' sprintf=[6]} </td>
                        <td>(0:00, 6:00, 12:00, 18:00, 24:00)</td>
                    </tr>
                    <tr>
                        <td>0 */12 * * *</td>
                        <td>{l s='Generate the feed twice a day at minute 0' mod='estimateddelivery'} </td>
                        <td>(0:00, 12:00)</td>
                    </tr>
                    <tr>
                        <td>0 0 * * *</td>
                        <td>{l s='Generate the feed once a day at minute 0' mod='estimateddelivery'} </td>
                        <td>(0:00)</td>
                    </tr>
                </tbody>
        </table>
            <br>
            <p>{l s='This behaviour can also be applied to the other modifiers, although you probably won\'t need to use it' mod='estimateddelivery'}.</p>
            <hr>
            <h3 class="modal-title text-info">{l s='Command' mod='estimateddelivery'} - {l s='The Feed Generation Script' mod='estimateddelivery'}</h3>
            <p>{l s='Once we know what schedule will be using, the next step is to call the script that will generate the feed. Depending on the system you use you will be asked for a URL, a path, or a command' mod='estimateddelivery'}</p>
            <p>{l s='Here you will find all the options, you just need to click on the one you need to use to copy it to your clipboard' mod='estimateddelivery'}.</p>
            <hr>
            <h4><strong>{l s='The URL' mod='estimateddelivery'}</strong> <a class="link_copy badge" data-url="{$DD_CRON_URL|escape:'htmlall':'UTF-8'}">{l s='Click to copy the URL' mod='estimateddelivery'}</a></h4>
            <p>{$DD_CRON_URL|escape:'htmlall':'UTF-8'}</p>
            <hr>
            <h4><strong>{l s='The Script Path' mod='estimateddelivery'}</strong> <a class="link_copy badge" data-url="{$DD_CRON_PATH|escape:'htmlall':'UTF-8'}">{l s='Click to copy the Path' mod='estimateddelivery'}</a></h4>
            <p>{$DD_CRON_PATH|escape:'htmlall':'UTF-8'} </p>
            <hr>
            <h4><strong>{l s='The Command' mod='estimateddelivery'}</strong></h4>
            <p>{l s='If you can use a command, you have two options' mod='estimateddelivery'}:</p>
            {if !isset($curl_enabled) || $curl_enabled}
                <p><strong>{l s='Use CURL: (recommended method)' mod='estimateddelivery'}</strong> <a class="link_copy badge" data-url="curl -l -k &quot;{$DD_CRON_URL|escape:'htmlall':'UTF-8'}&quot; >/dev/null 2>&1">{l s='Click to copy the curl command' mod='estimateddelivery'}</a><br>
         curl -l -k "{$DD_CRON_URL|escape:'htmlall':'UTF-8'}" >/dev/null 2>&1</p>
            {else}
                <p><strong>{l s='Use WGET: (recommended method)' mod='estimateddelivery'}</strong> <a class="link_copy badge" data-url="wget -O - -q -t 1 &quot;{$DD_CRON_URL|escape:'htmlall':'UTF-8'}&quot; >/dev/null 2>&">{l s='Click to copy the curl command' mod='estimateddelivery'}</a><br>
        wget -O - -q -t 1 "{$DD_CRON_URL|escape:'htmlall':'UTF-8'}" >/dev/null 2>& </p>
            {/if}
                <p><strong>{l s='Use the PHP CLI:' mod='estimateddelivery'}</strong> <a class="link_copy badge" data-url="{$PHP_CLI_PATH|escape:'htmlall':'UTF-8'} -q {$DD_CRON_PATH|replace:'?':' '|escape:'htmlall':'UTF-8'}">{l s='Click to copy the PHP CLI command' mod='estimateddelivery'}</a><br>
        {$PHP_CLI_PATH|escape:'htmlall':'UTF-8'} -q {$DD_CRON_PATH|replace:'?':' '|escape:'htmlall':'UTF-8'} </p>
            <p>{l s='If you have issues to complete the feed generation, try the PHP CLI option as the script won\'t have any restrictions with that method' mod='estimateddelivery'}</p>
            <hr>
            <h3 class="modal-title text-info">{l s='Output' mod='estimateddelivery'}</h3>
            <p>{l s='The output generated by the feed generation script should be always discarded' mod='estimateddelivery'}.</p>
            <p>{l s='The recommended method to do it is to add [1]%s[/1] after the cron job command' mod='estimateddelivery' tags=['<span class="badge">'] sprintf=['/dev/null 2>&1']}</p>
        {if !$isPS17}
            <hr>
            <h2>{l s='Alternative method' mod='estimateddelivery'}: {l s='Using the Prestashop Module' mod='estimateddelivery'}</h2>
            <div class="{if $old_ps}hint{else}alert alert-info{/if}">
                <p><strong>{l s='The easiest option, but it may not always execute' mod='estimateddelivery'}.</strong></p>
                <p>{l s='To be safe. Open the Cron Job\'s configuration page after your scheduled time and look for the field "last executed"' mod='estimateddelivery'}.</p>
                <p>{l s='If it says never, then the PrestaShop webservices aren\'t executing your cron job and you should try to configure it directly on your server (follow the steps below)' mod='estimateddelivery'}.</p>
                <p>{l s='If it shows a date then it\'s working fine and the products feed will be updated automatically' mod='estimateddelivery'}.</p>
            </div>
            {* <p><a href="{$CRON_MODULE_LINK|escape:'htmlall':'UTF-8'}" target="_blank" class="btn btn-primary">{l s='Click to open the' mod='estimateddelivery'} {l s='Cron tasks manager module' mod='estimateddelivery'}</a></p> *}
            <p>{l s='Basic Mode' mod='estimateddelivery'}</p>
            <ol>
            <li>{l s='Add new task' mod='estimateddelivery'}</li>
            <li>{l s='Enter a description' mod='estimateddelivery'}</li>
            <li>{l s='Paste the script url:' mod='estimateddelivery'}<br />
            {$DD_CRON_URL|escape:'htmlall':'UTF-8'}</li>
            <li>{l s='Select the hour of the day in the task frequency' mod='estimateddelivery'}</li>
            <li>{l s='Save.' mod='estimateddelivery'}</li>
            </ol>
            {* <p>{l s='It\'s important to check the cron module after the scheduled generation time to make sure the Feed is being generated.' mod='estimateddelivery'}</p>
            <p>{l s='To check it, go to the Cron Job module configuration page and look at the Cron Tasks table. Then look for the task you created and look at the value last execution, if you see the date of the last exection the module is working fine, but if you see Never that means the Cron Job module is not generating the Feed' mod='estimateddelivery'}</p> *}
        {/if}
        </div>
        {* <textarea id="temp_link"></textarea> *}
    </div>
    <hr>
    <div style="clear:both"></div>
    <div class="panel-footer" id="toolbar-footer">
        <button class="btn btn-default pull-right" id="submit_for_ddm" name="SubmitDDM" type="submit"><i class="process-icon-save"></i> <span>{l s='Save' mod='estimateddelivery'}</span></button>
    </div>
</form>
{if $old_ps == 1}
    </fieldset>
{else}
    </div>
{/if}
<script>
    var link_copied = '{l s='Copied to clipboard!' mod='estimateddelivery'}';
</script>

<script type="text/javascript">
    $(document).on('click', '.link_copy', function() {
        if (copyToClipboard($(this).data('url'))) {
            showSuccessMessage(link_copied);
        }
    });
    const copyToClipboard = str => {
        const el = document.createElement('textarea'); // Create a <textarea> element
        el.value = str; // Set its value to the string that you want copied
        el.setAttribute('readonly', ''); // Make it readonly to be tamper-proof
        el.style.position = 'absolute';
        el.style.left = '-9999px'; // Move outside the screen to make it invisible
        document.body.appendChild(el); // Append the <textarea> element to the HTML document
        const selected =
            document.getSelection().rangeCount > 0 // Check if there is any content selected previously
            ?
            document.getSelection().getRangeAt(0) // Store selection if found
            :
            false; // Mark as false to know no selection existed before
        el.select(); // Select the <textarea> content
        document.execCommand('copy'); // Copy - only works as a result of a user action (e.g. click events)
        document.body.removeChild(el); // Remove the <textarea> element
        if (selected) { // If a selection existed before copying
            document.getSelection().removeAllRanges(); // Unselect everything on the HTML document
            document.getSelection().addRange(selected); // Restore the original selection
        }
        return true;
    };
</script>
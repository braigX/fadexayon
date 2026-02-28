<nav id="hmd-modal" class="col-lg-3 hmd-sidebar-right hmd-sidebar-animate text-sm-center">
    <div class="hmd-container">
        <div class="hmd-header">
            <a href="#" class="hmd-dismiss-modal">×</a>
            <h2>Help</h2>
        </div>
        <div class="hmd-content">
            <div class="hmd-item" data-doc="enableGoogleConnect">
                <h2>{l s='Enable Google Connect' mod='higoogleconnect'}</h2>

                <div class="hmd-item-content">
                    <p>{l s='If disabled, Google connect buttons will be hidden from your website. Keep it enabled if you already configured the module.' mod='higoogleconnect'}</p>
                </div>
            </div>

            <div class="hmd-item" data-doc="googleClientId">
                <h2>{l s='Google Client ID' mod='higoogleconnect'}</h2>

                <div class="hmd-item-content">
                    <p>{l s='You\'ll need to create a new project in Google Console and configure it for Sign in button' mod='higoogleconnect'}</p>
                    <p><a href="https://www.youtube.com/watch?v=aBTt-nc-8Hw" target="_blank">{l s='Click here' mod='higoogleconnect'}</a> {l s='for detailed instructions on how to configure the App.' mod='higoogleconnect'}</p>
                </div>
            </div>

            <div class="hmd-item" data-doc="cleanDb">
                <h2>{l s='Clean Database when module uninstalled' mod='higoogleconnect'}</h2>

                <div class="hmd-item-content">
                    <p>{l s='We recommend to keep this option disabled. If you enable it, after uninstalling the module all data related to this module will be deleted from database.' mod='higoogleconnect'}</p>
                    <p>{l s='This option can be used if for some reason you don\'t want to use the module anymore or you need to reset all settings to defaults.' mod='higoogleconnect'}</p>
                </div>
            </div>
            
            <div class="hmd-item" data-doc="buttonPreview">
                <h2>{l s='Preview' mod='higoogleconnect'}</h2>

                <div class="hmd-item-content">
                    <p>{l s='This is how the button will display on your website. You can change it using below options.' mod='higoogleconnect'}</p>
                </div>
            </div>
            
            <div class="hmd-item" data-doc="buttonEnable">
                <h2>{l s='Enable' mod='higoogleconnect'}</h2>

                <div class="hmd-item-content">
                    <p>{l s='If enabled, the button will display on your website on current hook.' mod='higoogleconnect'}</p>
                </div>
            </div>
            
            <div class="hmd-item" data-doc="buttonType">
                <h2>{l s='Button Type' mod='higoogleconnect'}</h2>

                <div class="hmd-item-content">
                    <p>{l s='Select the button type that suits your theme better. There are 2 available options.' mod='higoogleconnect'}</p>
                    <p>{l s='The "Icon" type will display only the Google icon without any texts.' mod='higoogleconnect'}</p>
                    <p>{l s='The "Standart" type will display the Google icon and "Sign in" text, it\'ll also display user\'s name and email address if the user is logged in to their Google account.' mod='higoogleconnect'}</p>
                </div>
            </div>
            
            <div class="hmd-item" data-doc="buttonTheme">
                <h2>{l s='Button Theme' mod='higoogleconnect'}</h2>

                <div class="hmd-item-content">
                    <p>{l s='Select the button theme that suits your theme better. There are 3 available options: White, Blue and Black.' mod='higoogleconnect'}</p>
                </div>
            </div>
            
            <div class="hmd-item" data-doc="buttonShape">
                <h2>{l s='Button Shape' mod='higoogleconnect'}</h2>

                <div class="hmd-item-content">
                    <p>{l s='Select the button shape that suits your theme better. There are 2 available options: Rectangular and Pill.' mod='higoogleconnect'}</p>
                </div>
            </div>
            
            <div class="hmd-item" data-doc="buttonText">
                <h2>{l s='Button Text' mod='higoogleconnect'}</h2>

                <div class="hmd-item-content">
                    <p>{l s='This option allows you to select the text on button. This option is available only for the "Standart" button type.' mod='higoogleconnect'}</p>
                    <p>{l s='If you use multiple languages on your website, Google will translate the button for you.' mod='higoogleconnect'}</p>
                </div>
            </div>
            
            <div class="hmd-item" data-doc="buttonSize">
                <h2>{l s='Button Size' mod='higoogleconnect'}</h2>

                <div class="hmd-item-content">
                    <p>{l s='Select the button size that suits your theme better. There are 3 available options: Large, Medium and Small.' mod='higoogleconnect'}</p>
                </div>
            </div>
            
            <div class="hmd-item" data-doc="enableOneTapPrompt">
                <h2>{l s='Enable One Tap prompt' mod='higoogleconnect'}</h2>

                <div class="hmd-item-content">
                    <p>{l s='When this option is enabled, the module will display also One Tap Prompt on right top of your website except the traditional sign in button.' mod='higoogleconnect'}</p>
                </div>

                <div class="row hmd-images-block">
                    <div class="col-lg-6">
                        <a href="{$moduleAssetsDir|escape:'html':'UTF-8'}one-tap-prompt.jpg" class="hmd-image-item" target="_blank">
                            <img src="{$moduleAssetsDir|escape:'html':'UTF-8'}one-tap-prompt.jpg">
                            <span>{l s='One Tap Promt' mod='higoogleconnect'}</span>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="hmd-item" data-doc="googleConnectChartHelp">
                <h2>{l s='Statistics' mod='higoogleconnect'}</h2>

                <div class="hmd-item-content">
                    <p>{l s='Here you can view statistics of total registrations using Google Sign in method or the regular registration.' mod='higoogleconnect'}</p>
                    <p>{l s='You can configure it to display only for specific period or for all.' mod='higoogleconnect'}</p>
                </div>
            </div>
        </div>

        <div class="hmd-footer">
            {l s='Feel free to [1]Contact Us[/1] if you need further assistance.' tags=["<a href='{$contactLink}' target='_blank'>"] mod='higoogleconnect'}
        </div>
    </div>
</nav>
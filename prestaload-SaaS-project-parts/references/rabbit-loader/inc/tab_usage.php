<?php

if (class_exists('RabbitLoader_21_Tab_Usage')) {
    #it seems we have a conflict
    return;
}

final class RabbitLoader_21_Tab_Usage extends RabbitLoader_21_Tab_Init
{

    public static function init()
    {
        add_settings_section(
            'rabbitloader_section_usage',
            ' ',
            '',
            'rabbitloader-usage'
        );
    }

    public static function echoMainContent()
    {

        do_settings_sections('rabbitloader-usage');
        $overview = self::getOverviewData($apiError, $apiMessage);

?>
        <div class="" style="max-width: 1160px; margin:40px auto;">
            <div class="row mb-4">
                <div class="col-sm-12 col-md-4">
                    <?php self::quota_used_box($overview, false); ?>
                </div>
                <div class="col-sm-12 col-md-4">
                    <?php self::quota_remaining_box($overview); ?>
                </div>
                <div class="col-sm-12 col-md-4">
                    <div class="rl-mfe-card tpopup d-flex justify-content-between flex-column h-100" title="<?php RL21UtilWP::_e(sprintf('Your usage cycle will be reset on %s',  date('jS M, Y', strtotime($overview['plan_end_date'])))); ?>">
                        <h4 class="">
                            <?php echo date('jS', strtotime($overview['plan_end_date'])); ?> <small style="font-size:14px;"><?php echo date('M', strtotime($overview['plan_end_date'])); ?></small>
                        </h4>
                        <span class="text-secondary mt-2"><?php RL21UtilWP::_e('Next Quota Reset'); ?></span>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12">
                    <div id="mfe_page_views" style="height:400px; width:100%;max-width:100%;border: none; box-shadow:none;"></div>
                </div>
            </div>

        </div>
<?php
    }
}

?>
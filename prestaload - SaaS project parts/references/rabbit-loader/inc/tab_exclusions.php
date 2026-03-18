<?php

if (class_exists('RabbitLoader_21_Tab_Exclusions')) {
    #it seems we have a conflict
    return;
}

class RabbitLoader_21_Tab_Exclusions extends RabbitLoader_21_Tab_Init
{

    public static function init() {}

    public static function echoMainContent()
    {
        $isConnected = self::isPluginActivated();

        $urlparts = parse_url(home_url());
        $domain = $urlparts['scheme'] . '://' . $urlparts['host'];
        if (!empty($urlparts['port'])) {
            $domain .= ':' . $urlparts['port'];
        }


        if ($isConnected) {
?>
            <div class="" style="max-width: 1160px; margin:40px auto;">
                <?php
                self::excludeUrls();
                self::ignoreParams();
                ?>
            </div>
        <?php
        } ?>
    <?php
    }

    private static function saveNotice()
    {
        if (RL21UtilWP::is_flywheel()) {
            echo '<span class="d-block mt-2">', sprintf(RL21UtilWP::__('Flywheel Note: You need to Flush Cache manually from Flywheel dashboard after saving the settings <a href="%s">check details</a>'), "https://rabbitloader.com/kb/settings-for-flywheel/"), '</span>';
        }
    }
    private static function excludeUrls()
    {
        //depreciated@2.14.0
        RabbitLoader_21_Core::getWpOption($rl_wp_options);
        if (empty($rl_wp_options['exclude_patterns'])) {
            $rl_wp_options['exclude_patterns'] = '';
        }
        $exclude_patterns = $rl_wp_options['exclude_patterns'];

        //introduced@2.14.0
        RabbitLoader_21_Core::getWpUserOption($user_options);
        $shouldMigrate = !empty($exclude_patterns) && empty($user_options['exclude_patterns']);
        $userUpdating = isset($_POST['exclude_patterns']);
        if ($userUpdating || $shouldMigrate) {
            if ($userUpdating) {
                $user_options['exclude_patterns'] = sanitize_textarea_field($_POST['exclude_patterns']);
                RL21UtilWP::onPostChange(RL21UtilWP::POST_ID_ALL);
            } else {
                $user_options['exclude_patterns'] = $exclude_patterns;
            }
            RabbitLoader_21_Core::updateUserOption($user_options);
        }
        $exclude_patterns = $user_options['exclude_patterns'];

    ?>
        <div class="row mb-4">
            <div class="col">
                <div class="rl-mfe-card">
                    <div class="row">
                        <div class="col-sm-12 col-md-8 text-secondary">
                            <h5 class="mt-0">Exclude URLs</h5>
                            <span>Add URLs below to exclude them from optimization. Matching URLs will be skipped by RabbitLoader.</span>
                            <div class="mt-4">
                                <form method="post">
                                    <textarea class="form-control" rows="5" placeholder="e.g. /path/* without domain name" name="exclude_patterns"><?php echo $exclude_patterns; ?></textarea>
                                    <button type="submit" class="rl-mfe-btn mt-2">Save</button>
                                </form>
                                <?php
                                self::saveNotice();
                                ?>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <h5 class="mt-0">Guidelines</h5>
                            <ul class="" style="list-style:circle;">
                                <li>If the request URL matches the given <b>shell wildcard pattern</b>, it will not be optimized.</li>
                                <li>You can put one pattern per line</li>
                                <li>A wildcard character (*) can be used in the pattern. For example, /category/* will exclude all URLs starting with /category/.</li>
                                <li><a target="_blank" href="https://rabbitloader.com/kb/exclude-urls-from-cached/" title="Excluding URLs from being cached">Read documentation</a>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }

    private static function ignoreParams()
    {
        //depreciated@2.14.0
        RabbitLoader_21_Core::getWpOption($rl_wp_options);
        if (empty($rl_wp_options['ignore_params'])) {
            $rl_wp_options['ignore_params'] = '';
        }
        $ignore_params = $rl_wp_options['ignore_params'];

        //introduced@2.14.0
        RabbitLoader_21_Core::getWpUserOption($user_options);
        $shouldMigrate = !empty($ignore_params) && empty($user_options['ignore_params']);
        $userUpdating = isset($_POST['ignore_params']);
        if ($userUpdating || $shouldMigrate) {
            if ($userUpdating) {
                $user_options['ignore_params'] = sanitize_textarea_field($_POST['ignore_params']);
            } else {
                $user_options['ignore_params'] = $ignore_params;
            }
            RabbitLoader_21_Core::updateUserOption($user_options);
        }
        $ignore_params = $user_options['ignore_params'];
    ?>
        <div class="row mb-4">
            <div class="col">
                <div class="rl-mfe-card">
                    <div class="row">
                        <div class="col-sm-12 col-md-8 text-secondary">
                            <h5 class="mt-0">Ignore Parameters</h5>
                            <span>Query/GET parameters mentioned below will be ignored when creating cached copy of a page.</span>
                            <div class="mt-4">
                                <form method="post">
                                    <textarea class="form-control" rows="5" placeholder="e.g. fbclid" name="ignore_params"><?php echo $ignore_params; ?></textarea>
                                    <button type="submit" class="rl-mfe-btn mt-2">Save</button>
                                </form>
                                <?php
                                self::saveNotice();
                                ?>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <h5 class="mt-0">Guidelines</h5>
                            <ul class="" style="list-style:circle;">
                                <li>You can put one parameter per line.</li>
                                <li>Parameter name should be without any special characters such as &amp;, ?or =.</li>
                                <li>Regex or shell wildcard pattern can not be used here.</li>
                                <li>Many popular params are ignored by default, <a target="_blank" href="https://rabbitloader.com/kb/ignori-url-parameters-caching/" title="Ignoring URL parameters from caching key">read documentation</a>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<?php
    }
}

?>
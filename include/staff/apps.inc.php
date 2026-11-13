<?php
/*********************************************************************
    apps.inc.php

    Overview of the installed applications (redesigned): one section per
    panel with a card per application, the current panel first.

    Variables (scp/apps.php):
      $appSections  ['staff' | 'admin' => ['title' => ..., 'apps' => [...]]]

    An application is registered by a plugin with
      Application::registerStaffApp($desc, $href, $info)
      Application::registerAdminApp($desc, $href, $info)
    Used keys: desc, href, title (description) and the optional icon, a
    Font Awesome 3 class like 'icon-time' (default: puzzle piece).
    desc and title are output as given by the plugin (may contain entities).
**********************************************************************/
if(!defined('OSTSCPINC') || !$thisstaff) die('Access Denied');

// Name for sorting and display: without leading blanks / &nbsp;
$appName = function($app) {
    return trim(preg_replace('/^(\s|&nbsp;)+/i', '', (string) ($app['desc'] ?? '')));
};
$appIcon = function($app) {
    $icon = (string) ($app['icon'] ?? '');
    return preg_match('/^icon-[a-z0-9-]+$/', $icon) ? $icon : 'icon-puzzle-piece';
};
?>
<style>
    #AppOverview h2 { margin-bottom: 4px; }
    #AppOverview .app-section { margin: 22px 0 8px; }
    #AppOverview .app-section h3 {
        margin: 0 0 10px; padding-bottom: 6px;
        border-bottom: 1px solid #ddd; font-size: 15px;
    }
    #AppOverview .app-section h3 .app-count {
        display: inline-block; min-width: 18px; margin-left: 6px; padding: 1px 6px;
        border-radius: 9px; background: #eef3f8; color: #184E81;
        font-size: 12px; font-weight: normal; text-align: center;
    }
    #AppOverview .app-grid {
        display: grid; gap: 12px;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    }
    #AppOverview a.app-card {
        display: flex; align-items: flex-start; gap: 12px;
        padding: 14px 16px; border: 1px solid #ddd; border-radius: 4px;
        background: #fff; color: #444; text-decoration: none;
        transition: border-color .15s, box-shadow .15s;
    }
    #AppOverview a.app-card:hover, #AppOverview a.app-card:focus {
        border-color: #81a9d7; box-shadow: 0 2px 6px rgba(0,0,0,.12);
    }
    #AppOverview a.app-card .app-icon {
        flex: 0 0 auto; width: 36px; height: 36px; line-height: 36px;
        border-radius: 4px; background: #eef3f8; color: #184E81;
        font-size: 18px; text-align: center;
    }
    #AppOverview a.app-card .app-text { min-width: 0; }
    #AppOverview a.app-card .app-name {
        display: block; font-weight: bold; font-size: 14px; color: #184E81;
    }
    #AppOverview a.app-card:hover .app-name { text-decoration: underline; }
    #AppOverview a.app-card .app-desc {
        display: block; margin-top: 3px; color: #888; font-size: 12px; line-height: 1.4;
    }
    #AppOverview .app-empty { color: #888; font-style: italic; }
</style>
<div id="AppOverview">
    <h2><?php echo __('Applications'); ?></h2>
<?php foreach ($appSections as $key => $section) {
    $apps = $section['apps'] ?: array();
    usort($apps, function($a, $b) use ($appName) {
        return strcasecmp(strip_tags(html_entity_decode($appName($a))),
            strip_tags(html_entity_decode($appName($b))));
    });
?>
    <div class="app-section" id="app-section-<?php echo $key; ?>">
        <h3><?php echo sprintf(__('Currently Installed <b>%s</b> Applications'), $section['title']); ?>
            <span class="app-count"><?php echo count($apps); ?></span></h3>
<?php if (!$apps) { ?>
        <div class="app-empty"><?php echo __('No Applications installed for this category'); ?></div>
<?php } else { ?>
        <div class="app-grid">
<?php foreach ($apps as $app) { ?>
            <a class="app-card no-pjax" href="<?php echo $app['href']; ?>">
                <i class="app-icon <?php echo $appIcon($app); ?>"></i>
                <span class="app-text">
                    <span class="app-name"><?php echo $appName($app); ?></span>
<?php if (!empty($app['title'])) { ?>
                    <span class="app-desc"><?php echo $app['title']; ?></span>
<?php } ?>
                </span>
            </a>
<?php } ?>
        </div>
<?php } ?>
    </div>
<?php } ?>
</div>

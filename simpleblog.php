<?php
/**
 * Module Name: Simple Blog
 * Module ID:   simpleblogposts
 * Description: Lists the 5 most recent SimpleBlog posts with a link to add new.
 * Version:     1.0
 * Default W:   8
 * Default H:   4
 */

if (!defined('IN_GS')) { die('You cannot load this page directly.'); }

$uid = 'sbp_' . substr(md5(__FILE__), 0, 6);

global $live_plugins;
$posts      = array();
$plugin_ok  = function_exists('blog_init_db');
$db_file    = defined('GSDATAOTHERPATH') ? GSDATAOTHERPATH . 'blog.db' : '';
$db_ok      = $plugin_ok && $db_file && file_exists($db_file);

if ($db_ok) {
    try {
        $db  = new SQLite3($db_file, SQLITE3_OPEN_READONLY);
        $res = $db->query(
            "SELECT id, title, slug, date, published
             FROM posts
             ORDER BY date DESC
             LIMIT 5"
        );
        while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
            $posts[] = $row;
        }
        $db->close();
    } catch (Exception $e) {
        $db_ok = false;
    }
}
?>

<style>
#<?php echo $uid ?> .sb-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
}
#<?php echo $uid ?> .sb-table th {
    text-align: left;
    padding: 5px 8px;
    border-bottom: 2px solid #eee;
    color: #888;
    font-weight: 600;
    white-space: nowrap;
}
#<?php echo $uid ?> .sb-table td {
    padding: 5px 8px;
    border-bottom: 1px solid #f3f3f3;
    vertical-align: middle;
}
#<?php echo $uid ?> .sb-table tr:last-child td { border-bottom: none; }
#<?php echo $uid ?> .sb-table tr:hover td { background: #fafafa; }
#<?php echo $uid ?> .sb-title {
    font-weight: 500;
    color: #333;
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
#<?php echo $uid ?> .sb-date { color: #aaa; white-space: nowrap; }
#<?php echo $uid ?> .sb-actions { white-space: nowrap; }
#<?php echo $uid ?> .sb-btn {
    display: inline-block;
    padding: 4px 6px;
    border-radius: 4px;
    font-size: 11px;
    text-decoration: none;
    margin-left: 4px;
    border: 1px solid transparent;
    color: #fff;
}
#<?php echo $uid ?> .sb-btn-edit  { background: #5EBD3E; }
#<?php echo $uid ?> .sb-btn-edit:hover  { background: #4B9732; }
#<?php echo $uid ?> .sb-btn-new   { background: #4a90d9; font-size: 12px; padding: 4px 10px; }
#<?php echo $uid ?> .sb-btn-new:hover   { background: #357abd; }
#<?php echo $uid ?> .sb-badge {
    display: inline-block;
    padding: 2px 6px;
    border-radius: 10px;
    font-size: 10px;
    font-weight: 600;
}
#<?php echo $uid ?> .sb-badge-pub   { background: #d4edda; color: #28a745; }
#<?php echo $uid ?> .sb-badge-draft { background: #f8f9fa; color: #aaa; border: 1px solid #eee; }
#<?php echo $uid ?> .sb-empty { color: #bbb; font-style: italic; text-align: center; padding: 16px; }
#<?php echo $uid ?> .sb-missing {
    color: #856404;
    background: #fff3cd;
    border: 1px solid #ffeeba;
    border-radius: 6px;
    padding: 10px 12px;
    font-size: 13px;
}
#<?php echo $uid ?> .sb-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
}
#<?php echo $uid ?> .sb-header h3 { margin: 0; }
</style>

<div id="<?php echo $uid ?>">
    <div class="sb-header">
        <h3><svg xmlns="http://www.w3.org/2000/svg" style="vertical-align:middle;" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M4 21q-.825 0-1.412-.587T2 19V3l1.675 1.675L5.325 3L7 4.675L8.675 3l1.65 1.675L12 3l1.675 1.675L15.325 3L17 4.675L18.675 3l1.65 1.675L22 3v16q0 .825-.587 1.413T20 21zm0-2h7v-6H4zm9 0h7v-2h-7zm0-4h7v-2h-7zm-9-4h16V8H4z"/></svg> Simple Blog</h3>
        <a class="sb-btn sb-btn-new" href="load.php?id=simpleBlog&blog_admin&tab=add_post">+ New Post</a>
    </div>

    <?php if (!$plugin_ok): ?>
        <p class="sb-missing">⚠ SimpleBlog plugin is not active.</p>
    <?php elseif (!$db_ok): ?>
        <p class="sb-missing">⚠ SimpleBlog database not found.</p>
    <?php elseif (empty($posts)): ?>
        <p class="sb-empty">No posts found.</p>
    <?php else: ?>
    <table class="sb-table">
        <tr>
            <th>Title</th>
            <th>Date</th>
            <th>Status</th>
            <th style="text-align:center;">Action</th>
        </tr>
        <?php foreach ($posts as $post):
            $title   = htmlspecialchars($post['title']);
            $date    = date('Y-m-d', $post['date']);
            $editUrl = 'load.php?id=simpleBlog&blog_admin&tab=add_post&edit=' . (int)$post['id'];
        ?>
        <tr>
            <td class="sb-title" title="<?php echo $title; ?>"><?php echo $title; ?></td>
            <td class="sb-date"><?php echo $date; ?></td>
            <td>
                <?php if ($post['published']): ?>
                    <span class="sb-badge sb-badge-pub">Published</span>
                <?php else: ?>
                    <span class="sb-badge sb-badge-draft">Draft</span>
                <?php endif; ?>
            </td>
            <td class="sb-actions" style="text-align:center;">
                <a class="sb-btn sb-btn-edit" href="<?php echo $editUrl; ?>" title="Edit">
                    <svg xmlns="http://www.w3.org/2000/svg" style="vertical-align:middle;" width="14" height="14" viewBox="0 0 16 16"><rect width="16" height="16" fill="none"/><path fill="#fff" d="M10.529 1.764a2.621 2.621 0 1 1 3.707 3.707l-.779.779L9.75 2.543zM9.043 3.25L2.657 9.636a2.96 2.96 0 0 0-.772 1.354l-.87 3.386a.5.5 0 0 0 .61.608l3.385-.869a2.95 2.95 0 0 0 1.354-.772l6.386-6.386z"/></svg>
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>
</div>
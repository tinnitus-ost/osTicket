<?php
/*********************************************************************
    ajax.content.php

    AJAX interface for content fetching...allowed methods.

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
if(!defined('INCLUDE_DIR')) die('!');

require_once INCLUDE_DIR.'class.ajax.php';

class ContentAjaxAPI extends AjaxController {

    function syslog($id) {
        global $thisstaff;

        // Ensure there is a Staff and that they are an Administrator
        if (!$thisstaff || !$thisstaff->isAdmin())
            Http::response(403, 'Access Denied');

        if($id && ($log=Log::lookup($id))) {
            $content=sprintf('<div
                    style="width:500px;">&nbsp;<strong>%s</strong><br><p
                    style="white-space:pre-line;">%s</p>
                    <hr><strong>%s:</strong> <em>%s</em> <strong>%s:</strong> <em>%s</em></div>',
                    $log->getTitle(),
                    Format::display(str_replace(',',', ',Format::htmlchars($log->getText()))),
                    __('Log Date'),
                    Format::daydatetime($log->getCreateDate()),
                    __('IP Address'),
                    $log->getIP());
        }else {
            $content='<div style="width:295px;">&nbsp;<strong>'.__('Error').':</strong>'.
                sprintf(__('%s: Unknown or invalid ID.'), __('log entry')).'</div>';
        }

        return $content;
    }

    function ticket_variables() {

        $content='
<div style="width:680px;">
    <h2>'.__('Ticket Variables').'</h2>
    '.__('Please note that non-base variables depend on the context of use. Visit osTicket Wiki for up to date documentation.').'
    <br/>
    <table width="100%" border="0" cellspacing=1 cellpadding=2>
        <tr><td width="55%" valign="top"><b>'.__('Base Variables').'</b></td><td><b>'.__('Other Variables').'</b></td></tr>
        <tr>
            <td width="55%" valign="top">
                <table width="100%" border="0" cellspacing=1 cellpadding=1>
                    <tr><td width="130">%{ticket.id}</td><td>'.__('Ticket ID').' ('.__('internal ID').')</td></tr>
                    <tr><td>%{ticket.number}</td><td>'.__('Ticket Number').' ('.__('external ID').')</td></tr>
                    <tr><td>%{ticket.email}</td><td>'.__('Email Address').'</td></tr>
                    <tr><td>%{ticket.name}</td><td>'.__('Full Name').' &mdash;
                        <em>'.__('see name expansion').'</em></td></tr>
                    <tr><td>%{ticket.subject}</td><td>'.__('Subject').'</td></tr>
                    <tr><td>%{ticket.phone}</td><td>'.__('Phone number | ext').'</td></tr>
                    <tr><td>%{ticket.status}</td><td>'.__('Status').'</td></tr>
                    <tr><td>%{ticket.priority}</td><td>'.__('Priority').'</td></tr>
                    <tr><td>%{ticket.assigned}</td><td>'.__('Assigned Agent / Team').'</td></tr>
                    <tr><td>%{ticket.create_date}</td><td>'.__('Date Created').'</td></tr>
                    <tr><td>%{ticket.due_date}</td><td>'.__('Due Date').'</td></tr>
                    <tr><td>%{ticket.close_date}</td><td>'.__('Date Closed').'</td></tr>
                    <tr><td>%{ticket.recipients}</td><td>'.__('List of all recipient names').'</td></tr>
                    <tr><td nowrap>%{recipient.ticket_link}</td><td>'.__('Auth. token used for auto-login').'<br/>
                    '.__('Agent\'s ticket view link').'</td></tr>
                    <tr><td colspan="2" style="padding:5px 0 5px 0;"><em><b>'.__('Expandable Variables').'</b></em></td></tr>
                    <tr><td>%{ticket.topic}</td><td>'.__('Help Topic').'</td></tr>
                    <tr><td>%{ticket.dept}</td><td>'.__('Department').'</td></tr>
                    <tr><td>%{ticket.staff}</td><td>'.__('Assigned/closing agent').'</td></tr>
                    <tr><td>%{ticket.team}</td><td>'.__('Assigned/closing team').'</td></tr>
                    <tr><td>%{ticket.thread}</td><td>'.__('Ticket Thread').'</td></tr>
                    <tr><td colspan="2"><b>'.__('Name Expansion').'</b></td></tr>
                    <tr><td>.first</td><td>'.__('First Name').'</td></tr>
                    <tr><td>.last</td><td>'.__('Last Name').'</td></tr>
                    <tr><td>.full</td><td>'.__('First Last').'</td></tr>
                    <tr><td>.short</td><td>'.__('First L.').'</td></tr>
                    <tr><td>.shortformal</td><td>'.__('F. Last').'</td></tr>
                    <tr><td>.lastfirst</td><td>'.__('Last, First').'</td></tr>
                </table>
            </td>
            <td valign="top">
                <table width="100%" border="0" cellspacing=1 cellpadding=1>
                    <tr><td width="100">%{message}</td><td>'.__('Incoming message').'</td></tr>
                    <tr><td>%{response}</td><td>'.__('Outgoing response').'</td></tr>
                    <tr><td>%{comments}</td><td>'.__('Assign/transfer comments').'</td></tr>
                    <tr><td>%{note}</td><td>'.__('Internal note <em>(expandable)</em>').'</td></tr>
                    <tr><td>%{signature}</td><td>'.__('Signature').'</td></tr>
                    <tr><td>%{assignee}</td><td>'.__('Assigned Agent / Team').'</td></tr>
                    <tr><td>%{assigner}</td><td>'.__('Agent assigning the ticket').'</td></tr>
                    <tr><td>%{url}</td><td>'.__('osTicket\'s base url (FQDN)').'</td></tr>
                    <tr><td>%{reset_link}</td>
                        <td>'.__('Reset link used by the password reset feature').'</td></tr>
                </table>
                <table width="100%" border="0" cellspacing=1 cellpadding=1>
                    <tr><td colspan="2" style="padding:5px 0 5px 0;"><em><b>'.__('Ticket Thread expansions').'</b></em></td></tr>
                    <tr><td>.original</td><td>'.__('Original Message').'</td></tr>
                    <tr><td>.lastmessage</td><td>'.__('Last Message').'</td></tr>
                    <tr><td colspan="2" style="padding:5px 0 5px 0;"><em><b>'.__('Thread Entry expansions').'</b></em></td></tr>
                    <tr><td>.poster</td><td>'.__('Poster').'</td></tr>
                    <tr><td>.create_date</td><td>'.__('Date Created').'</td></tr>
                    <tr><td>.title</td><td>'.__('Subject, if any').'</td></tr>
                    <tr><td>.body</td><td>'.__('Message body').'</td></tr>
                    <tr><td>.files</td><td>'.__('Attached files').'</td></tr>
                    <tr><td>.create_date</td><td>'.__('Date created').'</td></tr>
                    <tr><td>.ip_address</td><td>'.__('IP address of remote user, for web submissions').'</td></tr>
                    <tr><td>.poster</td><td>'.__('Submitter of the thread item').'</td></tr>
                    <tr><td>.staff</td><td>'.__('Agent posting the note or response').'</td></tr>
                </table>
            </td>
        </tr>
    </table>
</div>';

        return $content;
    }

// Anpassung Anfang: separate tooltip "Supported Variables" for tasks
    function task_variables() {

        // Placeholder cells must never wrap - "%{comments}" broken into
        // "%" + "{comments}" is unreadable and blows up the row height.
        $ph = 'style="white-space:nowrap;padding-right:12px"';
        $grp = 'style="padding:7px 0 2px 0;"';

        $content='
<div style="width:740px;">
    <h2>'.__('Task Variables').'</h2>
    '.__('Please note that non-base variables depend on the context of use. Visit osTicket Wiki for up to date documentation.').'
    <br/>
    <table width="100%" border="0" cellspacing=1 cellpadding=2 style="table-layout:fixed">
        <tr>
            <td width="52%" valign="top"><b>'.__('Base Variables').'</b></td>
            <td valign="top"><b>'.__('Name Expansion').'</b></td>
        </tr>
        <tr>
            <td valign="top">
                <table width="100%" border="0" cellspacing=1 cellpadding=1>
                    <tr><td '.$ph.' width="150">%{task.id}</td><td>'.__('Task ID').' ('.__('internal ID').')</td></tr>
                    <tr><td '.$ph.'>%{task.number}</td><td>'.__('Task Number').'</td></tr>
                    <tr><td '.$ph.'>%{task.title}</td><td>'.__('Title').'</td></tr>
                    <tr><td '.$ph.'>%{task.subject}</td><td>'.__('Subject').'</td></tr>
                    <tr><td '.$ph.'>%{task.status}</td><td>'.__('Status').'</td></tr>
                    <tr><td '.$ph.'>%{task.create_date}</td><td>'.__('Date Created').'</td></tr>
                    <tr><td '.$ph.'>%{task.due_date}</td><td>'.__('Due Date').'</td></tr>
                    <tr><td '.$ph.'>%{task.close_date}</td><td>'.__('Date Closed').'</td></tr>
                    <tr><td '.$ph.'>%{task.last_update}</td><td>'.__('Time of last update').'</td></tr>
                    <tr><td '.$ph.'>%{task.description}</td><td>'.__('Description').'</td></tr>
                    <tr><td '.$ph.'>%{task.recipients}</td><td>'.__('List of all recipient names').'</td></tr>
                    <tr><td colspan="2" '.$grp.'><em><b>'.__('Expandable Variables').'</b></em></td></tr>
                    <tr><td '.$ph.'>%{task.dept}</td><td>'.__('Department').'</td></tr>
                    <tr><td '.$ph.'>%{task.creator}</td><td>'.__('Agent who created the task').'</td></tr>
                    <tr><td '.$ph.'>%{task.assigned}</td><td>'.__('Assigned Agent / Team').'</td></tr>
                    <tr><td '.$ph.'>%{task.staff}</td><td>'.__('Assigned/closing agent').'</td></tr>
                    <tr><td '.$ph.'>%{task.team}</td><td>'.__('Assigned/closing team').'</td></tr>
                    <tr><td '.$ph.'>%{task.thread}</td><td>'.__('Task Thread').'</td></tr>
                    <tr><td '.$ph.'>%{task.staff_link}</td><td>'.__('Link to view the task').'</td></tr>
                    <tr><td '.$ph.'>%{task.ticket_link}</td><td>'.__('Link to view the task inside the ticket').'</td></tr>
                    <tr><td colspan="2" '.$grp.'><em><b>'.__('Task activity templates').'</b></em></td></tr>
                    <tr><td '.$ph.'>%{message}</td><td>'.__('Message').' ('.__('New Task').')</td></tr>
                    <tr><td '.$ph.'>%{note}</td><td>'.__('Internal note').' ('.sprintf(__('Update %s'), __('Task')).')</td></tr>
                    <tr><td '.$ph.'>%{activity}</td><td>'.__('Type of recent activity').'</td></tr>
                    <tr><td '.$ph.'>%{creator}</td><td>'.__('Agent who created the task').'</td></tr>
                    <tr><td '.$ph.'>%{poster}</td><td>'.__('Poster').'</td></tr>
                    <tr><td colspan="2" '.$grp.'><em><b>'.__('Task assignment and transfer templates').'</b></em></td></tr>
                    <tr><td '.$ph.'>%{comments}</td><td>'.__('Assign/transfer comments').'</td></tr>
                    <tr><td '.$ph.'>%{assignee}</td><td>'.__('Assigned Agent / Team').'</td></tr>
                    <tr><td '.$ph.'>%{assigner}</td><td>'.__('Agent performing the assignment').'</td></tr>
                    <tr><td '.$ph.'>%{staff}</td><td>'.__('Agent performing the transfer').'</td></tr>
                </table>
            </td>
            <td valign="top">
                <table width="100%" border="0" cellspacing=1 cellpadding=1>
                    <tr><td '.$ph.'>.name.first</td><td>'.__('First Name').'</td></tr>
                    <tr><td '.$ph.'>.name.last</td><td>'.__('Last Name').'</td></tr>
                    <tr><td '.$ph.'>.name.full</td><td>'.__('First Last').'</td></tr>
                    <tr><td '.$ph.'>.name.short</td><td>'.__('First L.').'</td></tr>
                    <tr><td '.$ph.'>.name.shortformal</td><td>'.__('F. Last').'</td></tr>
                    <tr><td '.$ph.'>.name.lastfirst</td><td>'.__('Last, First').'</td></tr>
                    <tr><td colspan="2" '.$grp.'><em><b>'.__('Task Thread expansions').'</b></em><br>( %{task.thread} )</td></tr>
                    <tr><td '.$ph.'>.complete</td><td>'.__('Thread Correspondence').'</td></tr>
                    <tr><td '.$ph.'>.lastmessage</td><td>'.__('Last Message').'</td></tr>
                    <tr><td '.$ph.'>.original</td><td>'.__('Original Message').'</td></tr>
                    <tr><td colspan="2" '.$grp.'><em><b>'.__('Thread Entry expansions').'</b></em><br>( %{message} / %{note} )</td></tr>
                    <tr><td '.$ph.'>.title</td><td>'.__('Subject, if any').'</td></tr>
                    <tr><td '.$ph.'>.body</td><td>'.__('Message body').'</td></tr>
                    <tr><td '.$ph.'>.files</td><td>'.__('Attached files').'</td></tr>
                    <tr><td '.$ph.'>.create_date</td><td>'.__('Date created').'</td></tr>
                    <tr><td '.$ph.'>.ip_address</td><td>'.__('IP address of remote user, for web submissions').'</td></tr>
                    <tr><td '.$ph.'>.poster</td><td>'.__('Submitter of the thread item').'</td></tr>
                    <tr><td '.$ph.'>.staff</td><td>'.__('Agent posting the note or response').'</td></tr>
                    <tr><td colspan="2" '.$grp.'><em><b>'.__('Other Variables').'</b></em></td></tr>
                    <tr><td '.$ph.' width="115">%{recipient}</td><td>'.__('Message recipient').'</td></tr>
                    <tr><td '.$ph.'>%{url}</td><td>'.__('osTicket\'s base url (FQDN)').'</td></tr>
                    <tr><td '.$ph.'>%{company}</td><td>'.__('Company Information').'</td></tr>
                </table>
            </td>
        </tr>
    </table>
</div>';

        return $content;
    }
// Anpassung Ende: separate tooltip "Supported Variables" for tasks
    function getSignature($type, $id=null) {
        global $thisstaff;

        if (!$thisstaff)
            Http::response(403, 'Login Required');

        switch ($type) {
        case 'none':
            break;
        case 'agent':
            if (!($staff = Staff::lookup($id)))
                Http::response(404, 'No such staff member');
            echo Format::viewableImages($staff->getSignature());
            break;
        case 'mine':
            echo Format::viewableImages($thisstaff->getSignature());
            break;
        case 'dept':
            if (!($dept = Dept::lookup($id)))
                Http::response(404, 'No such department');
            echo Format::viewableImages($dept->getSignature());
            break;
        default:
            Http::response(400, 'Unknown signature type');
            break;
        }
    }

    function manageContent($id, $lang=false) {
        global $thisstaff, $cfg;

        if (!$thisstaff || !$thisstaff->isAdmin())
            Http::response(403, 'Access Denied');

        $content = Page::lookup($id, $lang);

        $langs = Internationalization::getConfiguredSystemLanguages();
        $translations = $content->getAllTranslations();
        $info = array(
            'title' => $content->getName(),
            'body' => $content->getBody(),
        );
        foreach ($translations as $t) {
            if (!($data = $t->getComplex()))
                continue;
            $info['trans'][$t->lang] = array(
                'title' => $data['name'],
                'body' => $data['body'],
            );
        }

        include STAFFINC_DIR . 'templates/content-manage.tmpl.php';
    }

    function manageNamedContent($type, $lang=false) {
        global $thisstaff, $cfg;

        if (!$thisstaff || !$thisstaff->isAdmin())
            Http::response(403, 'Access Denied');

        $langs = $cfg->getSecondaryLanguages();

        $content = Page::lookupByType($type, $lang);
        $info = $content->getHashtable();

        include STAFFINC_DIR . 'templates/content-manage.tmpl.php';
    }

    function updateContent($id) {
        global $thisstaff, $cfg;

        if (!$thisstaff || !$thisstaff->isAdmin())
            Http::response(403, 'Access Denied');
        elseif (!($content = Page::lookup($id)))
            Http::response(404, 'No such content');

        if (!isset($_POST['body']))
            $_POST['body'] = '';

        $vars = array_merge($content->getHashtable(), $_POST);
        $errors = array();

        // Allow empty content for the staff banner
        if ($content->update($vars, $errors,
            $content->getType() == 'banner-staff')
        ) {
            Http::response(201, 'Have a great day!');
        }
        if (!$errors['err'])
            $errors['err'] = __('Correct any errors below and try again.');
        $langs = Internationalization::getConfiguredSystemLanguages();
        $info = $_POST;
        $errors = Format::htmlchars($errors);
        include STAFFINC_DIR . 'templates/content-manage.tmpl.php';
    }

    function context() {
        global $thisstaff;

        if (!$thisstaff)
            Http::response(403, 'Login Required');
        if (!$_GET['root'])
            Http::response(400, '`root` is required parameter');

        $items = VariableReplacer::getContextForRoot($_GET['root']);

        if (!$items)
            Http::response(422, 'No such context');

        header('Content-Type: application/json');
        return $this->encode($items);
    }
}
?>

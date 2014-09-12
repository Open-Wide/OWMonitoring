{*
Variables that can be set in a set-block to customize the e-mail sent :
- subject (default is "Report <report_name>" )
- content_type (like described in site.ini/[MailSettings].ContentType)
- email_sender (default is site.ini/[MailSettings].AdminEmail)
- email_receiver (default is site.ini/[UserSettings].RegistrationEmail. If not set, it is site.ini/[MailSettings].AdminEmail
*}
*** Report {$report.report_name} at {$report.date} on {cond( $report.hostnames, $report.hostnames|implode(', '), 'unknown host')}

{foreach $report.datas as $data_identifier => $data_value}
{if $data_value|count()|eq(1)}
{$data_identifier} : {$data_value.0.data}
{else}
{$data_identifier} :
{foreach $data_value as $data_item}
{$data_item.data}
{delimiter}
---------------------------
{/delimiter}
{/foreach}
{/if}
{delimiter}

===========================

{/delimiter}
{/foreach}




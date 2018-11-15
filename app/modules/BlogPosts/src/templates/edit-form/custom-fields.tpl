{*
 * Create/ edit post custom form fields
 *
 * Variables:
 *
 * Array $customSettingFields - File path to any template files
 * containing form fields to output on the post edit form
 *}
{foreach $customSettingFields as $fieldTemplate}
    {include file="$fieldTemplate"}
{/foreach}
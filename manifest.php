<?php
$manifest = array(
    'acceptable_sugar_versions' => array(
        'regex_matches' => array(
            '12.*.*',
            '13.*.*',
            '14.*.*'
        ),
    ),
    'acceptable_sugar_flavors' => array(
        'PRO',
        'ENT',
        'ULT'
    ),
    'readme' => '',
    'key' => 'Lead Conversion for SugarCRM',
    'author' => 'Rafael Silva Nercessian',
    'description' => 'Lead conversion for SugarCRM',
    'icon' => '',
    'is_uninstallable' => true,
    'name' => 'Lead Conversion for SugarCRM',
    'published_date' => '2024-07-04 21:24:17',
    'type' => 'module',
    'version' => '202407043',
    'remove_tables' => false,
);

$installdefs = array(
    'id' => 'lead_conversion',
    'post_install' => array('<basepath>/post_install.php'),
    'layoutfields' => array(
        array(
            'additional_fields' => array(
                'Leads' => 'convert_lead_c',
            ),
        ),
    ),
    'custom_fields' => array(
        array(
            'name' => 'convert_lead_c',
            'label' => 'LBL_CONVERT_LEAD_C',
            'type' => 'bool',
            'module' => 'Leads',
            'help' => 'Please click here and save the record to convert this lead',
            'comment' => '',
            'default_value' => '',
            'required' => false,
            'reportable' => true,
            'audited' => false,
            'importable' => true,
            'duplicate_merge' => true,
            'ext1' => '',
            'ext2' => '',
            'ext3' => '',
            'ext4' => '',
        )
    ),
    'logic_hooks' => array(
        array(
            'module' => 'Leads',
            'hook' => 'after_save',
            'order' => 1,
            'description' => 'Check Lead Conversion Options',
            'file' => 'custom/modules/Leads/Leads_Hook.php',
            'class' => 'Leads_Hook',
            'function' => 'leadConversionOptions',
        ),
        array(
            'module' => 'Leads',
            'hook' => 'after_save',
            'order' => 2,
            'description' => 'Convert Leads',
            'file' => 'custom/modules/Leads/Leads_Hook.php',
            'class' => 'Leads_Hook',
            'function' => 'convertLeads',
        ),
        array(
            'module' => 'Leads',
            'hook' => 'after_save',
            'order' => 3,
            'description' => 'Create Account',
            'file' => 'custom/modules/Leads/Leads_Hook.php',
            'class' => 'Leads_Hook',
            'function' => 'createAccount',
        ),
        array(
            'module' => 'Leads',
            'hook' => 'after_save',
            'order' => 4,
            'description' => 'Create Contact',
            'file' => 'custom/modules/Leads/Leads_Hook.php',
            'class' => 'Leads_Hook',
            'function' => 'createContact',
        ),
        array(
            'module' => 'Leads',
            'hook' => 'after_save',
            'order' => 5,
            'description' => 'Create Opportunity',
            'file' => 'custom/modules/Leads/Leads_Hook.php',
            'class' => 'Leads_Hook',
            'function' => 'createOpportunity',
        ),
        array(
            'module' => 'Leads',
            'hook' => 'after_save',
            'order' => 6,
            'description' => 'Create Relationship',
            'file' => 'custom/modules/Leads/Leads_Hook.php',
            'class' => 'Leads_Hook',
            'function' => 'createRelationships',
        ),
    ),
    'copy' => array(
        array(
            'from' => '<basepath>/Files/custom/modules/Leads/Leads_Hook.php',
            'to' => 'custom/modules/Leads/Leads_Hook.php',
        )
    )
);
<?php
$settings->add(new admin_setting_heading(
            'headerconfig',
            get_string('headerconfig', 'block_estandarcl'),
            get_string('descconfig', 'block_estandarcl')
        ));

$settings->add(new admin_setting_configcheckbox(
            'estandarcl/Allow_HTML',
            get_string('labelallowhtml', 'block_estandarcl'),
            get_string('descallowhtml', 'block_estandarcl'),
            '0'

        ));

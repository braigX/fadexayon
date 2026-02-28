<?php
/**
 * Web Browser Push Notifications using OneSignal
 *
 * @author    Rolige <www.rolige.com>
 * @copyright Since 2011 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

class RgPuNoModuleFormMaintenance extends RgPuNoModuleForm
{
    private $available_notification_types;

    public function __construct()
    {
        parent::__construct();

        $this->menu_active = 'maintenance';
        $this->submit_action = 'submit' . Tools::ucfirst($this->menu_active) . 'Form';
        $this->p .= 'CLEAN_';

        $this->available_notification_types = [
            'delivered' => $this->l('Delivered'),
            'queued' => $this->l('Queued'),
            'scheduled' => $this->l('Scheduled'),
            'canceled' => $this->l('Canceled'),
            'norecipients' => $this->l('No recipients'),
        ];
    }

    public function getFormFields()
    {
        $form = [];

        $available_notification_type = [];

        foreach ($this->available_notification_types as $id => $name) {
            $available_notification_type[] = ['id_group' => $id, 'name' => $name];
        }

        $form[] = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Maintenance'),
                    'icon' => 'icon-eraser',
                ],
                'input' => [
                    [
                        'type' => 'rg-group-box',
                        'label' => $this->l('Clear notifications'),
                        'name' => $this->p . 'CLEAN',
                        'head' => [
                            'name' => $this->l('Notification Type'),
                        ],
                        'values' => [
                            'query' => $available_notification_type,
                            'id' => 'id_group',
                            'name' => 'name',
                        ],
                        'max_height' => 296,
                        'hint' => $this->l('Types of notifications to be deleted. They could be cleaned by cron job or Clear Now button.'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Range'),
                        'name' => $this->p . 'RANGE',
                        'class' => 'fixed-width-sm',
                        'suffix' => $this->l('days'),
                        'prefix' => '>=',
                        'hint' => $this->l('Range of days after notification creation to be considered for deleting.'),
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Clear unsubscribed subscribers'),
                        'name' => $this->p . 'UNSUBSCRIBED',
                        'required' => false,
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                        'hint' => $this->l('Delete unsubscribed subscribers already.'),
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
                'buttons' => [
                    'clean_now' => [
                        'name' => 'clean_now',
                        'type' => 'submit',
                        'title' => $this->l('Clear Now'),
                        'class' => 'btn btn-default',
                        'icon' => 'process-icon-eraser',
                    ],
                ],
            ],
        ];

        return $form;
    }

    public function getFormValues($for_save = false)
    {
        $fields_value = [
            ($name = $this->p . 'UNSUBSCRIBED') => (int) (bool) Tools::getValue($name, Configuration::get($name)),
            ($name = $this->p . 'RANGE') => abs((int) Tools::getValue($name, Configuration::get($name))),
        ];

        RgPuNoTools::getRgGroupBoxValue(
            $this->isSubmitForm(),
            $name = $this->p . 'CLEAN',
            array_keys($this->available_notification_types),
            Configuration::get($name),
            $fields_value,
            $for_save
        );

        return $fields_value;
    }

    public function validateForm()
    {
        $val = $this->getFormValues(true);
        $panel = $this->l('Maintenance') . ' > ';

        if (Tools::isSubmit('clean_now')) {
            if (!$val[$this->p . 'CLEAN'] && !$val[$this->p . 'UNSUBSCRIBED']) {
                return $panel . $this->l('You should select at least one clear option.');
            }
        } elseif (array_diff(explode(',', $val[$this->p . 'CLEAN']), array_keys($this->available_notification_types))) {
            return $panel . $this->l('Clear notifications') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a value from the list.');
        }

        return false;
    }

    public function isSubmitForm()
    {
        if (Tools::isSubmit('clean_now')) {
            return true;
        }

        return parent::isSubmitForm();
    }

    public function processForm()
    {
        if (Tools::isSubmit('clean_now')) {
            $val = $this->getFormValues(true);

            RgPuNoNotification::cleanNotifications($val[$this->p . 'CLEAN'], $val[$this->p . 'RANGE']);
            RgPuNoSubscriber::cleanUnsubscribed((int) $val[$this->p . 'UNSUBSCRIBED']);

            return $this->l('Notifications cleared successfully.');
        }

        return parent::processForm();
    }
}

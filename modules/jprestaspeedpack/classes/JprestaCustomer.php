<?php
/**
 * Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
 *
 *    @author    Jpresta
 *    @copyright Jpresta
 *    @license   See the license of this module in file LICENSE.txt, thank you.
 */

use JPresta\SpeedPack\JprestaUtils;

if (!defined('_PS_VERSION_')) {
    exit;
}

// Check existence of the class to be able to handle compatibility problems in a friendly way
if (!class_exists('JprestaCustomer')) {
    /**
     * Used to create fake user to anonimize the cache
     */
    class JprestaCustomer extends Customer
    {
        public function validateFields($die = true, $error_return = false)
        {
            // Compatibility with LoyaltyRewardPoints module
            if (isset($this->points) && Module::isEnabled('LoyaltyRewardPoints')) {
                unset($this->points);
                unset($this->webserviceParameters['fields']['points']);
                unset($this->def['fields']['points']);
            }

            // Set fake value to required fields to avoid SQL errors and disable all fields validation to avoid
            // functional errors
            foreach ($this->def['fields'] as $fieldName => $fieldDef) {
                if (in_array('required', $fieldDef) && ((bool) $fieldDef['required'])) {
                    if (Tools::isEmpty($this->$fieldName)) {
                        if ($fieldName === 'email') {
                            // A module is updating our fake customer and we don't want that!
                            JprestaUtils::addLog('PageCache | Fake customer is updated and it must not! ' . JprestaUtils::getStackTrace());
                            if ($die) {
                                throw new PrestaShopException('This fake customer must not be updated, contact the support of Page Cache Ultimate (JPresta) to fix this');
                            }

                            return $error_return ? 'This fake customer must not be updated, contact the support of Page Cache Ultimate (JPresta) to fix this' : false;
                        }
                        if (in_array('default', $fieldDef) && !Tools::isEmpty($fieldDef['default'])) {
                            // Use default value if any
                            $this->$fieldName = $fieldDef['default'];
                        } else {
                            if ($fieldDef['type'] == self::TYPE_INT) {
                                $this->$fieldName = 0;
                            } elseif ($fieldDef['type'] == self::TYPE_BOOL) {
                                $this->$fieldName = 0;
                            } elseif ($fieldDef['type'] == self::TYPE_HTML) {
                                $this->$fieldName = '<!-- -->';
                            } elseif ($fieldDef['type'] == self::TYPE_STRING) {
                                $this->$fieldName = '-';
                            } elseif ($fieldDef['type'] == self::TYPE_FLOAT) {
                                $this->$fieldName = 0.0;
                            } elseif ($fieldDef['type'] == self::TYPE_DATE) {
                                $this->$fieldName = '1970-01-01';
                            }
                        }
                    }
                }
            }
            if (property_exists($this, 'cpf')) {
                // A fake CPF for Brazilian users
                $this->cpf = '783.472.095-37';
            }
            return true;
        }

        public static function getOrCreateCustomerWithSameGroups($customer, $dontCheckLogged = false)
        {
            if (!$customer || (!$dontCheckLogged && !(method_exists($customer, 'isLogged') && $customer->isLogged()))) {
                // The visitor is not logged in
                $id_default_group = (int) Configuration::get('PS_UNIDENTIFIED_GROUP');
                $ids_groups = [$id_default_group];
            } else {
                $id_default_group = (int) $customer->id_default_group;
                if ($id_default_group === 0) {
                    $id_default_group = (int) Configuration::get('PS_UNIDENTIFIED_GROUP');
                }
                if (!Group::isFeatureActive()) {
                    $ids_groups = [$id_default_group];
                } else {
                    $ids_groups = Customer::getGroupsStatic($customer->id);
                }
                // Put the default group at the beginning
                foreach ($ids_groups as $arrayKey => $groupId) {
                    if ($groupId === $id_default_group) {
                        $ids_groups[$arrayKey] = $ids_groups[0];
                        $ids_groups[0] = $id_default_group;
                    }
                }
            }

            $currentCacheKeyUserGroupConf = Jprestaspeedpack::getCacheKeyForUserGroups(Shop::getContextShopID());
            if (array_key_exists($id_default_group, $currentCacheKeyUserGroupConf) && $currentCacheKeyUserGroupConf[$id_default_group]['specific_cache']) {
                $anonymousKey = 'd' . $id_default_group;
            } else {
                $anonymousKey = 'd0';
            }
            $displayKeys = [];
            foreach ($ids_groups as $id_group) {
                if (array_key_exists($id_group, $currentCacheKeyUserGroupConf)) {
                    // Default group must be at the beginning (or at least at the same place)
                    if (!in_array($currentCacheKeyUserGroupConf[$id_group]['display_key'], $displayKeys)) {
                        $displayKeys[] = $currentCacheKeyUserGroupConf[$id_group]['display_key'];
                    }
                    if (Configuration::get('pagecache_depend_on_other_groups')
                        && $id_group !== $id_default_group
                        && $currentCacheKeyUserGroupConf[$id_group]['specific_cache']
                    ) {
                        // Secondary groups must also be in the cache key
                        $anonymousKey .= '|'.$id_group;
                    }
                }
            }
            $anonymousKey .= '-' . md5(implode('|', $displayKeys));

            $anonymousCustomer = new JprestaCustomer();
            $anonymousCustomer = $anonymousCustomer->getByEmail($anonymousKey . '@fakeemail.com');
            if (!$anonymousCustomer) {
                $anonymousCustomer = new JprestaCustomer();
                $anonymousCustomer->email = $anonymousKey . '@fakeemail.com';
                $anonymousCustomer->note = 'Groups=' . implode(',', $ids_groups) . ' Key=' . implode('|', $displayKeys);
                $anonymousCustomer->active = false;
                $anonymousCustomer->firstname = 'fake-user-for-pagecache';
                $anonymousCustomer->lastname = 'do-not-delete';
                $anonymousCustomer->passwd = 'WhateverSinceItIsInactive0_';
                $anonymousCustomer->id_default_group = $id_default_group;
                $addResult = $anonymousCustomer->add();
                if (!$addResult) {
                    JprestaUtils::addLog('PageCache | Failed to create fake customer ' . $anonymousCustomer->email . ' - ' . $anonymousCustomer->note . ' - Error: ' . \Db::getInstance()->getMsgError(), 3);
                }
                else {
                    $anonymousCustomer->updateGroup($ids_groups);
                    JprestaUtils::addLog('PageCache | Fake customer ' . $anonymousCustomer->email . ' created - ' . $anonymousCustomer->note);
                    if (Module::isInstalled('shaim_gdpr')) {
                        Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'customer` SET `shaim_gdpr_active` = 1 WHERE `id_customer` = ' . (int)$anonymousCustomer->id . ';');
                    }
                    // Create a guest for this customer to avoid error in statsdata
                    JprestaUtils::dbExecuteSQL('INSERT INTO `' . _DB_PREFIX_ . 'guest` (`id_customer`) VALUES (' . (int)$anonymousCustomer->id . ');');
                }
            }
            if (!$customer || (!$dontCheckLogged && !(method_exists($customer, 'isLogged') && $customer->isLogged()))) {
                $anonymousCustomer->id = null;
            } else {
                $anonymousCustomer->id = (int) $anonymousCustomer->id;
            }

            // Remove some informations so they are not visible in cache
            $anonymousCustomer->firstname = '';
            $anonymousCustomer->lastname = '';
            $anonymousCustomer->email = '';

            // Avoid the customer to be considered has banned but we want to keep the customer as disabled in Db
            Cache::store('Customer::isBanned_' . (int) $anonymousCustomer->id, false);

            return $anonymousCustomer;
        }

        /**
         * Overrides default behavior to simulates logged in states for HTML cache
         */
        public function isLogged($withGuest = false)
        {
            if (!$withGuest && $this->is_guest == 1) {
                return false;
            }

            if (JprestaUtils::isCaller('ps_googleanalytics', 'run')) {
                // Don't want Google Analytics to use the ID of fake user.
                return false;
            }

            if (JprestaUtils::isCaller('dm_gdpr', 'hookDisplayHeader')) {
                // Don't want dm_gdpr to redirect to /module/dm_gdpr/gdpr.
                return false;
            }

            if (JprestaUtils::isCaller('kd_timetoriffconnector', 'hookHeader')) {
                // Don't want kd_timetoriffconnector to modify our fake customer (ticket #4397).
                return false;
            }

            if (JprestaUtils::isCaller('rcpgtagmanager', 'initModel')) {
                // Don't want rcpgtagmanager to send datas with our fake customer.
                return false;
            }

            return $this->id && Validate::isUnsignedId($this->id);
        }

        public static function purge()
        {
            $shopGroup = Shop::getGroupFromShop(Shop::getContextShopID(), false);
            if (Shop::getContext() == Shop::CONTEXT_SHOP && $shopGroup['share_customer']) {
                $whereShop = '`id_shop_group`=' . (int) Shop::getContextShopGroupID();
            } else {
                $whereShop = '`id_shop` IN (' . implode(', ', Shop::getContextListShopID(Shop::SHARE_CUSTOMER)) . ')';
            }
            $rows = JprestaUtils::dbSelectRows('SELECT id_customer FROM `' . _DB_PREFIX_ . 'customer` WHERE email like \'%@fakeemail.com\' AND active=0 AND ' . $whereShop);
            foreach ($rows as $row) {
                $customerToDelete = new JprestaCustomer($row['id_customer']);
                $currentJprestaCustomer = self::getOrCreateCustomerWithSameGroups($customerToDelete, true);
                if ($currentJprestaCustomer->id !== $customerToDelete->id) {
                    JprestaUtils::addLog('PageCache | Fake customer ' . $customerToDelete->email . ' deleted - ' . $customerToDelete->note);
                    $customerToDelete->delete();
                }
            }
        }

        public static function deleteAllFakeUsers()
        {
            $rows = JprestaUtils::dbSelectRows('SELECT id_customer FROM `' . _DB_PREFIX_ . 'customer` WHERE email like \'%@fakeemail.com\' AND active=0');
            foreach ($rows as $row) {
                $customerToDelete = new JprestaCustomer($row['id_customer']);
                $customerToDelete->delete();
            }
            JprestaUtils::addLog('PageCache | All fake customers deleted');
        }

        public static function isVisitor($id_customer)
        {
            $customer = new Customer($id_customer);
            if (Validate::isLoadedObject($customer)) {
                if (!Group::isFeatureActive()) {
                    $ids_groups = [$customer->id_default_group];
                } else {
                    $ids_groups = Customer::getGroupsStatic($id_customer);
                }

                return count($ids_groups) === 1
                    && $ids_groups[0] == $customer->id_default_group
                    && $customer->id_default_group == (int) Configuration::get('PS_UNIDENTIFIED_GROUP');
            }

            return false;
        }

        public function getLabel()
        {
            $groupList = '';
            if (!Group::isFeatureActive()) {
                $groupIds = [$this->id_default_group];
            } else {
                $groupIds = Customer::getGroupsStatic($this->id);
            }
            // Put the default group at the beginning
            foreach ($groupIds as $arrayKey => $groupId) {
                if ($groupId === $this->id_default_group) {
                    $groupIds[$arrayKey] = $groupIds[0];
                    $groupIds[0] = $this->id_default_group;
                }
            }
            foreach ($groupIds as $index => $groupId) {
                $group = new Group($groupId);
                if (!empty($groupList)) {
                    $groupList .= ', ';
                }
                if (is_array($group->name)) {
                    if (array_key_exists(Context::getContext()->cookie->id_lang, $group->name)) {
                        $groupList .= $group->name[Context::getContext()->cookie->id_lang];
                    } else {
                        $groupList .= $group->name[0];
                    }
                } else {
                    $groupList .= $group->name;
                }
                if ($index === 0) {
                    $groupList .= '*';
                }
            }

            return $groupList;
        }
    }
}

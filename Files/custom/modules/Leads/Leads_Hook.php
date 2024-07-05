<?php

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

class Leads_Hook
{
    const CONVERTED = 'Converted';

    public function leadConversionOptions($bean, $event, $arguments)
    {
        if (empty($bean->leadConversionOptionsSet) && !empty($bean->convert_lead_c)) {
            $GLOBALS['log']->fatal("Inside Leads_Hook convertLeads");
            $bean->leadConversionOptionsSet = true;
            global $sugar_config;
            if ($sugar_config['lead_conv_activity_opt'] != 'donothing') {
                $configuratorObj = new Configurator();
                $configuratorObj->loadConfig();
                $configuratorObj->config['lead_conv_activity_opt'] = "donothing";
                $configuratorObj->saveConfig();
            }
        }
    }

    public function convertLeads($bean, $event, $arguments)
    {
        if (empty($bean->leadConverted) && !empty($bean->convert_lead_c)) {
            $GLOBALS['log']->fatal("Inside Leads_Hook convertLeads");
            $bean->leadConverted = true;
            $bean->converted = true;
            $bean->status = self::CONVERTED;
            $bean->date_converted = TimeDate::getInstance()->nowDb();
            $bean->save();
        }
    }

    public function createAccount($bean, $event, $arguments)
    {
        if (empty($bean->accountCreated) && !empty($bean->convert_lead_c)) {
            $GLOBALS['log']->fatal("Inside Leads_Hook createAccount");
            $bean->accountCreated = true;
            $sugarQuery = new SugarQuery();
            $sugarQuery->from(BeanFactory::newBean('Accounts'));
            $sugarQuery->select(array('id'));
            $sugarQuery->where()->equals("name", $bean->name);
            $GLOBALS['log']->fatal("Found account query: " . $sugarQuery->compile());
            $existingAccount = $sugarQuery->getOne();
            if (empty($existingAccount)) {
                $accountsBean = BeanFactory::newBean('Accounts');
                $accountsBean->name = $bean->name;
                $accountsBean->billing_address_street = $bean->primary_address_street;
                $accountsBean->billing_address_city = $bean->primary_address_city;
                $accountsBean->billing_address_state = $bean->primary_address_state;
                $accountsBean->billing_address_postalcode = $bean->primary_address_postalcode;
                $accountsBean->shipping_address_street = $bean->primary_address_street;
                $accountsBean->shipping_address_city = $bean->primary_address_city;
                $accountsBean->shipping_address_state = $bean->primary_address_state;
                $accountsBean->shipping_address_postalcode = $bean->primary_address_postalcode;
                $accountsBean->shipping_address_country = $bean->primary_address_country;
                $bean->account = $accountsBean->save();
                $GLOBALS['log']->fatal("Account Id: " . print_r($bean->account->id, 1));
            }
        }
    }

    public function createContact($bean, $event, $arguments)
    {
        if (empty($bean->contactCreated) && !empty($bean->convert_lead_c)) {
            $GLOBALS['log']->fatal("Inside Leads_Hook contactCreated");
            $bean->contactCreated = true;
            $sugarQuery = new SugarQuery();
            $sugarQuery->from(BeanFactory::newBean('Contacts'));
            $sugarQuery->select(array('id'));
            $sugarQuery->where()->equals("first_name", $bean->first_name)->queryAnd()->equals("last_name", $bean->last_name);
            $GLOBALS['log']->fatal("Found contact query: " . $sugarQuery->compile());
            $existingContact = $sugarQuery->getOne();
            if (empty($existingContact)) {
                $contactsBean = BeanFactory::newBean('Contacts');
                $contactsBean->name = $bean->first_name . ' ' . $bean->last_name;
                $contactsBean->first_name = $bean->first_name;
                $contactsBean->last_name = $bean->last_name;
                if (!empty($bean->email1)) {
                    $contactsBean->email1 = $bean->email1;
                } elseif (!empty($bean->emailAddress)) {
                    $emailAddresses = $bean->emailAddress->getAddresses();
                    if (!empty($emailAddresses[0]['email_address'])) {
                        $contactsBean->email1 = $emailAddresses[0]['email_address'];
                    }
                }
                $bean->contactId = $contactsBean->save();
                $GLOBALS['log']->fatal("Contact Id: " . print_r($bean->contactId, 1));
            }
        }
    }

    public function createOpportunity($bean, $event, $arguments)
    {
        if (empty($bean->opportunityCreated) && !empty($bean->convert_lead_c)) {
            $bean->opportunityCreated = true;
            $GLOBALS['log']->fatal("Inside Leads_Hook createOpportunity");
            $opportunityBean = BeanFactory::newBean('Opportunities');
            $opportunityBean->name = $bean->first_name . ' ' . $bean->last_name . " - " . date("Y-m-d");
            $opportunityBean->sales_stage = 'Prospecting';  // Set an initial sales stage
            $opportunityBean->lead_source = $bean->lead_source;
            $opportunityBean->description = $bean->description;
            $bean->opportunityId = $opportunityBean->save();
            $GLOBALS['log']->fatal("Opportunity Id: " . print_r($bean->opportunityId, 1));

        }
    }

    public function createRelationships($bean)
    {
        if (empty($bean->createRelationship)) {
            $bean->createRelationship = true;
            // Lead -> Account, Contact, Opportunity
            $bean->load_relationship('accounts');
            $bean->accounts->add($bean->account->id);
            $bean->load_relationship('contacts');
            $bean->contacts->add($bean->contactId);
            $bean->load_relationship('opportunity');
            $bean->opportunity->add($bean->opportunityId);

            // Account -> Contact, Opportunity
            $account = BeanFactory::getBean('Accounts', $bean->account->id);
            $account->load_relationship('contacts');
            $account->contacts->add($bean->contactId);
            $account->load_relationship('opportunities');
            $account->opportunities->add($bean->opportunityId);

            // Contact -> Account, Opportunity
            $contact = BeanFactory::getBean('Contacts', $bean->contactId);
            $contact->load_relationship('accounts');
            $contact->accounts->add($bean->account->id);
            $contact->load_relationship('opportunities');
            $contact->opportunities->add($bean->opportunityId);

            // Opportunity -> Account, Contact
            $opportunity = BeanFactory::getBean('Opportunities', $bean->opportunityId);
            $opportunity->load_relationship('accounts');
            $opportunity->accounts->add($bean->account->id);
            $opportunity->load_relationship('contacts');
            $opportunity->contacts->add($bean->contactId);
        }
    }
}



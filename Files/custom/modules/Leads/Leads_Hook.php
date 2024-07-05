<?php

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

class Leads_Hook
{
    const CONVERTED = 'Converted';

    public function leadConversionOptions($bean, $event, $arguments)
    {
        if (empty($bean->leadConversionOptionsSet) && !empty($bean->convert_lead_c) && empty($bean->converted)) {
            $GLOBALS['log']->debug("Inside Leads_Hook leadConversionOptions");
            $bean->leadConversionOptionsSet = true;
            $this->updateLeadConversionConfig();
        }
    }

    private function updateLeadConversionConfig()
    {
        global $sugar_config;
        if ($sugar_config['lead_conv_activity_opt'] != 'donothing') {
            $configurator = new Configurator();
            $configurator->loadConfig();
            $configurator->config['lead_conv_activity_opt'] = "donothing";
            $configurator->saveConfig();
        }
    }

    public function convertLeads($bean, $event, $arguments)
    {
        if (empty($bean->leadConverted) && !empty($bean->convert_lead_c) && empty($bean->converted)) {
            $GLOBALS['log']->debug("Inside Leads_Hook leadConverted");
            $bean->leadConverted = true;
            $bean->status = self::CONVERTED;
            $bean->date_converted = TimeDate::getInstance()->nowDb();
        }
    }

    public function createAccount($bean, $event, $arguments)
    {
        if (empty($bean->accountCreated) && !empty($bean->convert_lead_c) && empty($bean->converted)) {
            $GLOBALS['log']->debug("Inside Leads_Hook createAccount");
            $bean->accountCreated = true;
            $existingAccount = $this->findExistingAccount($bean->name);
            if (empty($existingAccount)) {
                $account = BeanFactory::newBean('Accounts');
                $account->name = $bean->name;
                $this->copyAddressToAccount($bean, $account);
                $bean->account = $account->save();
                $GLOBALS['log']->debug("Account id: " . print_r($bean->account->id, 1));
            }
        }
    }

    private function findExistingAccount($name)
    {
        $sugarQuery = new SugarQuery();
        $sugarQuery->from(BeanFactory::newBean('Accounts'));
        $sugarQuery->select(array('id'));
        $sugarQuery->where()->equals("name", $name);
        return $sugarQuery->getOne();
    }

    private function copyAddressToAccount($from, $to)
    {
        $addressFields = ['street', 'city', 'state', 'postalcode', 'country'];
        foreach ($addressFields as $field) {
            $billingField = "billing_address_" . $field;
            $shippingField = "shipping_address_" . $field;
            $primaryField = "primary_address_" . $field;

            $to->$billingField = $from->$primaryField;
            $to->$shippingField = $from->$primaryField;
        }
    }

    public function createContact($bean, $event, $arguments)
    {
        if (empty($bean->contactCreated) && !empty($bean->convert_lead_c) && empty($bean->converted)) {
            $GLOBALS['log']->debug("Inside Leads_Hook createContact");
            $bean->contactCreated = true;
            $existingContact = $this->findExistingContact($bean->first_name, $bean->last_name);
            if (empty($existingContact)) {
                $contact = BeanFactory::newBean('Contacts');
                $contact->name = $bean->first_name . ' ' . $bean->last_name;
                $contact->first_name = $bean->first_name;
                $contact->last_name = $bean->last_name;
                $contact->email1 = $this->getLeadEmail($bean);
                $bean->contactId = $contact->save();
                $GLOBALS['log']->debug("Contact id: " . print_r($bean->contactId, 1));
            }
        }
    }

    private function findExistingContact($firstName, $lastName)
    {
        $sugarQuery = new SugarQuery();
        $sugarQuery->from(BeanFactory::newBean('Contacts'));
        $sugarQuery->select(array('id'));
        $sugarQuery->where()->equals("first_name", $firstName)->queryAnd()->equals("last_name", $lastName);
        return $sugarQuery->getOne();
    }

    private function getLeadEmail($bean)
    {
        if (!empty($bean->email1)) {
            return $bean->email1;
        } elseif (!empty($bean->emailAddress)) {
            $emailAddresses = $bean->emailAddress->getAddresses();
            return !empty($emailAddresses[0]['email_address']) ? $emailAddresses[0]['email_address'] : '';
        }
        return '';
    }

    public function createOpportunity($bean, $event, $arguments)
    {
        if (empty($bean->opportunityCreated) && !empty($bean->convert_lead_c) && empty($bean->converted)) {
            $GLOBALS['log']->debug("Inside Leads_Hook createOpportunity");
            $bean->opportunityCreated = true;
            $opportunity = BeanFactory::newBean('Opportunities');
            $opportunity->name = $bean->first_name . ' ' . $bean->last_name . " - " . date("Y-m-d");
            $opportunity->sales_stage = 'Prospecting';
            $opportunity->lead_source = $bean->lead_source;
            $opportunity->description = $bean->description;
            $bean->opportunityId = $opportunity->save();
            $GLOBALS['log']->debug("Opportunity id: " . print_r($bean->opportunityId, 1));
        }
    }

    public function createRelationships($bean)
    {
        if (empty($bean->createRelationship) && !empty($bean->convert_lead_c) && empty($bean->converted)) {
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
            $bean->converted = true;
            $bean->save();
        }
    }
}
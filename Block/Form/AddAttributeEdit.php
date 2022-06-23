<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace AHT\AttributeCustomer\Block\Form;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\AccountManagementInterface;

class AddAttributeEdit extends \Magento\Customer\Block\Account\Dashboard
{
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $customerAccountManagement,
        array $data = []
    ) {
        $this->countryCollectionFactory = $countryCollectionFactory;
        $this->countryFactory = $countryFactory;
        $this->customerSession = $customerSession;
        $this->subscriberFactory = $subscriberFactory;
        $this->customerRepository = $customerRepository;
        $this->customerAccountManagement = $customerAccountManagement;
        parent::__construct(
            $context,
            $customerSession,
            $subscriberFactory,
            $customerRepository,
            $customerAccountManagement,
            $data
        );
    }


    public function getCountryName($countryCode)
    {
        $country = $this->countryFactory->create()->loadByCode($countryCode);
        return $country->getName();
    }

    public function getCountryCollection()
    {
        $collection = $this->countryCollectionFactory->create()
            ->join(
                'msp_tfa_country_codes',
                'main_table.country_id = msp_tfa_country_codes.code',
                [
                    'msp_tfa_country_codes.name',
                    'msp_tfa_country_codes.dial_code'
                ]
            )
            ->setOrder('main_table.country_id', 'ASC')
            ->loadByStore();
        return $collection;
    }

    public function getCountries()
    {
        $countryCollection = $this->getCountryCollection();

        $countries = [];
        foreach ($countryCollection->getData() as $country) {
            $countries[$country['country_id']] = $this->getCountryName($country['country_id']);
        }

        return $countries;
    }

    public function getPhoneCountryCodes($getCountryCode = true)
    {
        $phone = $this->getCustomer()->getCustomAttribute('contact_phone')->getValue();
        $array = explode(' ', $phone);
        $check = count($array);
        $countryCode = '';

        foreach ($array as $key => $value) {
            if ($check < 3) {
                if ($key == 0) {
                    $countryCode = $value;
                } else {
                    $number = $value;
                }
            } else {
                if ($key == 0) {
                    $countryCode = $value;
                } elseif ($key < 3 && $key != 0) {
                    $countryCode .= $value;
                } else {
                    $number = $value;
                }
            }
        }
        if ($getCountryCode == true) {
            return $countryCode;
        } else {
            return $number;
        }
    }
}

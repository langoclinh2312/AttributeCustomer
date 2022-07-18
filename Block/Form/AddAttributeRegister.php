<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace AHT\AttributeCustomer\Block\Form;

class AddAttributeRegister extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        array $data = []
    ) {
        $this->countryCollectionFactory = $countryCollectionFactory;
        $this->countryFactory = $countryFactory;
        parent::__construct($context, $data);
        $this->_template = 'AHT_AttributeCustomer::additionalinfocustomer.phtml';
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
}

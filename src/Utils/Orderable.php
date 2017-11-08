<?php

namespace Arbory\Merchant\Utils;

interface Orderable
{
    /**
     * Get the item ID
     *
     * @return string
     */
    public function getId();

    /**
     * Get the item Class
     *
     * @return string
     */
    public function getClass();

    /**
     * Get the item price without vat
     *
     * @return int
     */
    public function getPrice() : int;

    /**
     * Get the item price with vat included
     * @return int
     */
    public function getPriceVat() : int;

    /**
     * Get the VAT tax value like (21.5% => 21.5)
     *
     * @return int
     */
    public function getVatRate() : float;


    /**
     * Get the item summary
     *
     * @return string
     */
    public function getSummary();

    /**
     * Get the item options
     *
     * @return array
     */
    public function getOptions();

    /**
     * Get the item quantity
     *
     * @return int
     */
    public function getQuantity();

    /**
     * Get the items total price without VAT
     *
     * @return int
     */
    public function getTotal() : int;

    /**
     * Get the items total price with VAT and qty
     *
     * @return int
     */
    public function getTotalVat() : int;
}

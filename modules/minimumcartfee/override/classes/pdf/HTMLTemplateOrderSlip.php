<?php
class HTMLTemplateOrderSlipOverride extends HTMLTemplateOrderSlipCore
{
    public function getContent()
    {
        //  -- Avant tout, calcule et injecte le "minimum cart fee" --
        $row = Db::getInstance(_PS_USE_SQL_SLAVE_)
            ->getRow('SELECT fee_amount FROM ' . _DB_PREFIX_ . 'minimumcartfee_order
                      WHERE id_order = ' . (int)$this->order_slip->id_order);

        $minAmount = (float) Configuration::get('MINCARTFEE_MIN_AMOUNT') ?: 0;
        $label = Configuration::get('MINCARTFEE_FEE_NAME') ?: 'Frais de commande';
        $fee = isset($row['fee_amount']) ? $minAmount : 0;

        // 1) On modifie directement les totaux de l'objet OrderSlip/Order
        // if ($fee > 0) {
        //     $this->order->total_paid_tax_excl -= $fee;
        //     $this->order->total_paid_tax_incl -= $fee;
        // }

        // 2) On assigne le montant brut à la même instance Smarty que celle utilisée ensuite
        $this->smarty->assign('minimum_cart_fee_amount', $fee);
        $this->smarty->assign('minimum_cart_fee_amount_label', $label);

        // Puis on continue normalement
        return parent::getContent();
    }
}

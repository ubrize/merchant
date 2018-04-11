<?php

namespace Arbory\Merchant\Utils\Handlers;

use Illuminate\Http\Request;
use Arbory\Merchant\Models\Transaction;
use Arbory\Merchant\Utils\GatewayHandler;

class SebLinkHandler extends GatewayHandler
{
    /**
     * @param Request $request
     * @return string
     */
    public function getTransactionReference(Request $request): string
    {
        return $request->get('IB_PAYMENT_ID', '');
    }

    /**
     * @param Transaction $transaction
     * @return array
     */
    public function getPurchaseArguments(Transaction $transaction): array
    {
        return [
            'transactionReference' => $transaction->token_id
        ];
    }

    /**
     * @param string $suggestedLanguage
     * @return string
     */
    public function getLanguage(string $suggestedLanguage): string
    {
        $defaultLangauge = 'LAT';
        $codeToSupportedLang = [
            'lv' => 'LAT',
            'ru' => 'RUS',
            'en' => 'ENG'
        ];
        if (isset($codeToSupportedLang[$suggestedLanguage])) {
            return $codeToSupportedLang[$suggestedLanguage];
        }
        return $defaultLangauge;
    }
}

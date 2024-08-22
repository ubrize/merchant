<?php

namespace Arbory\Merchant\Utils\Handlers;

use Illuminate\Http\Request;
use Arbory\Merchant\Models\Transaction;
use Arbory\Merchant\Utils\GatewayHandler;

class SwedbankBanklinkHandler extends GatewayHandler
{
    public function getTransactionReference(Request $request) : string
    {
        return $request->get('VK_STAMP', '');
    }

    public function getPurchaseArguments(Transaction $transaction) : array
    {
        // order reference id should be passed on implementation if necessary
        return [
            'transactionReference' => $transaction->token_id
        ];
    }

    public function getLanguage(string $suggestedLanguage): string
    {
        $defaultLangauge = 'LAT';
        $codeToSupportedLang = [
            'lv' => 'LAT',
            'ru' => 'RUS',
            'en' => 'ENG'
        ];
        if(isset($codeToSupportedLang[$suggestedLanguage])){
            return $codeToSupportedLang[$suggestedLanguage];
        }
        return $defaultLangauge;
    }
}

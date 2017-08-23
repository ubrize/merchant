<?php

namespace Arbory\Merchant\Utils;

use Omnipay\Common\GatewayInterface;

class GatewayHandlerFactory
{
    private $classMap = [
        'Omnipay\FirstDataLatvia\Gateway' => FirstDataLatviaHandler::class,
        'Omnipay\SwedbankBanklink\Gateway' => SwedbankBanklinkHandler::class
    ];

    public function create(GatewayInterface $gatewayInterface): GatewayHandler
    {
        $gatewayClassName = get_class($gatewayInterface);

        if(isset($this->classMap[$gatewayClassName])) {
            $formatterClass = $this->classMap[$gatewayClassName];
            return new $formatterClass();
        }

        throw new \InvalidArgumentException('Unknown gateway type given');
    }

    public function addHandler($gatewayName, $formatterClass){
        $this->classMap[$gatewayName] = $formatterClass;
    }
}
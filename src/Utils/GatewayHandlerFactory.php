<?php

namespace Arbory\Merchant\Utils;

use Omnipay\Common\GatewayInterface;

class GatewayHandlerFactory
{
    private $classMap = [
        'Omnipay\FirstDataLatvia\Gateway' => FirstDataLatviaHandler::class
    ];

    public function create(GatewayInterface $gatewayInterface): GatewayHandler
    {
        $formatter = $this->getClass(get_class($gatewayInterface));

        if($formatter){
            return $formatter;
        }

        throw new \InvalidArgumentException('Unknown gateway type given');
    }

    private function getClass($gatewayClassName): GatewayHandler
    {
        if(isset($this->classMap[$gatewayClassName])) {
            $formatterClass = $this->classMap[$gatewayClassName];
            return new $formatterClass();
        }

        return null;
    }

    public function addHandler($gatewayName, $formatterClass){
        $this->classMap[$gatewayName] = $formatterClass;
    }
}
<?php
namespace Gbili\Miner\Blueprint\Action\GetContents\Contents;

class FileGetContents
implements ContentsFetcherInterface, \Gbili\Miner\EventManagerAwareSharedManagerExpectedInterface
{
    use \Zend\EventManager\EventManagerAwareTrait;

    public function fetch(\Gbili\Url\UrlInterface $url)
    {
        //Allow listeners to apply a different length delay
        $responses = $this->getEventManager()->trigger(
            __FUNCTION__,
            $this,
            ['url' => $url]
        );

        $delayMin = 1;
        $delayMax = $delayMin;
        if ($responses->stopped()) {
            $listenersReturn = $responses->last();
            if (is_array($listenersReturn) && isset($listenersReturn['min']) && isset($listenersReturn['max'])) {
                $delayMin = (integer) $listenersReturn['min'];
                $delayMax = (integer) $listenersReturn['max'];
            } else if (is_numeric($listenersReturn)) {
                $delayMin = (integer) $listenersReturn;
                $delayMax = $delayMin;
            }
        }

        $delay = new \Gbili\Stdlib\Delay($delayMin, $delayMax);
        $delay->apply();

        $result = file_get_contents($url->toString());

        if (false !== $result) {
    	    $result = \Gbili\Encoding\Encoding::utf8Encode($result);
        }
	    return $result;
    }
}

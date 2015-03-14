<?php
namespace Gbili\Miner;

/**
 * Classes having listeners that want to
 * attach to them, the ListenersAttacher
 * will get the attachable class listeners
 * with getListeners() and attach them to
 * the attachable.
 * 
 * @author g
 *
 */
interface AttachableListenersInterface
{
    public function getListeners();
}

<?php
namespace Gbili\Stdlib\Gauge;

/**
 * This class is to be used as a negative burst monitor
 * With a maximal positive limit, that cannot be exceeded
 * but will not throw if one tries to exceed it. But
 * again it will not be exceeded.
 * 
 * You have to define the max, that will not be exceeded.
 * We use a default max so it is not infinite. But you 
 * can set it to inifinite if you want with 0.
 * 
 * The count could start at 5 (it does by default), and 
 * every time you call substract($num=1), we check if count
 * has reached 0. If so, it will throw on next call to
 * subtract(), except if you call add(), which would
 * revive the count.
 * 
 * @author g
 *
 */
class PositiveGauge extends Gauge
{
    use PositiveGaugeTrait {
        PositiveGaugeTrait::__construct insteadof Gauge;
    }
}
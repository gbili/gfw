<?php
namespace Gbili\Stdlib\Gauge;

/**
 * This class is to be used as a maxCount burst monitor
 * 
 * You have to define the max, that you want to monitor.
 * We use a default max to make sure it is not infinite
 * which would not make sense since it is ...StrictMax.
 * 
 * The count could start at 0 (it does by default), and 
 * every time you call add($num=1), we check if the max 
 * has been reached.
 * 
 * @author g
 *
 */
class MaxGauge extends Gauge
{
    use MaxGaugeTrait {
        MaxGaugeTrait::__construct insteadof Gauge;
    }
}
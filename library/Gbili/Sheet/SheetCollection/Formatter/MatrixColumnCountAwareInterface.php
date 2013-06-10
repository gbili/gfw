<?php
namespace Gbili\Sheet\SheetCollection\Formatter;

interface MatrixColumnCountAwareInterface
{
    /**
     * @return number
     */
    public function getMatrixColumnCount();
}
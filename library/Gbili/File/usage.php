<?php
use Gbili\Sheet\SheetCollection\SheetCollection;

$s = new SheetCollection();
$s->setConfig(array(
  //'formatter'                    => 'Gbili\Sheet\Collection\Formatter\MatrixFormatter'
    'split_long_lines_into_matrix' => true,
    'input_filename'               => __DIR__ . '/document_root/d/ZF2CallStack.txt',
  //'sheet_max_lines'              => 36,
  //'lines_max_length'             => 50,
  //'file_class'                   => 'Gbili\File\File',
));

$s->format();

// Output the whole matrix to a single file
echo $s->toString();

/*
// For every sheet create a file
$i = 0;
foreach ($s as $sheet) {
    file_put_contents(__DIR__ . "/document_root/d/zf2matrix-$i.php", $sheet->toString());
    ++$i;
}
*/
<?php
namespace App\Reg\Person\Transformer;

use Symfony\Component\Form\DataTransformerInterface;

class ShirtSizeTransformer implements DataTransformerInterface
{
    // List of shirt sizes should be tied to project shirt size form control?
    private $shirtSizes = [
        'na'         =>     'na',
        'youths'     =>     'YS',
        'youthm'     =>     'YM',
        'youthl'     =>     'YL',
        'adults'     =>     'AS',
        'adultm'     =>     'AM',
        'adultl'     =>     'AL',
        'adultlx'    =>    'ALX',
        'adultlxx'   =>   'ALXX',
        'adultlxxx'  =>  'ALXXX',
        'adultlxxxx' => 'ALXXXX',
    ];
    public function transform($value)
    {
        return isset($this->shirtSizes[$value]) ? $this->shirtSizes[$value] : '???';
    }
    public function reverseTransform($value)
    {
        $key = array_search($value,$this->shirtSizes);
        return $key ? $key : 'na';
    }
}
?>

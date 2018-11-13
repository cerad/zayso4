<?php
namespace App\Reg\Person\Transformer;

use Symfony\Component\Form\DataTransformerInterface;

class PhoneTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        $value = preg_replace('/\D/','',$value);

        if (!$value) return $value;

        return sprintf('(%s) %s-%s',substr($value,0,3),substr($value,3,3),substr($value,6,4));
    }
    public function reverseTransform($value)
    {
        return preg_replace('/\D/','',$value);
    }
}


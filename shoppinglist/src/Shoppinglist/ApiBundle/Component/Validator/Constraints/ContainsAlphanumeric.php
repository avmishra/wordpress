<?php
namespace Shoppinglist\ApiBundle\Component\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ContainsAlphanumeric extends Constraint
{
    public $message = 'Only letters and numbers are allowed.';
    
    public function validatedBy()
    {
        return get_class($this).'Validator';
    }
}
<?php
namespace App\Annotation;
/**
 * @Annotation
 */
class MailDocumentValidator
{
    public $property;
    public $entity;
    public $invalidMessage=null;
}
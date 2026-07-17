<?php

namespace App\Traits;

trait SafeRelationships
{
    public function getRelationValue($key)
    {
        $value = parent::getRelationValue($key);

        if ($value === null && method_exists($this, $key)) {
            $reflection = new \ReflectionMethod($this, $key);
            $docComment = $reflection->getDocComment();

            if ($docComment && (strpos($docComment, 'hasOne') !== false || strpos($docComment, 'belongsTo') !== false)) {
                return new NullRelationship();
            }
        }

        return $value;
    }
}

class NullRelationship
{
    public function __get($name)
    {
        return null;
    }

    public function __call($name, $arguments)
    {
        return null;
    }

    public function __toString()
    {
        return '';
    }
}

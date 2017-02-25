<?php

namespace Skvn\Base\Traits;

use Skvn\Base\Helpers\Str;

trait SelfDescribe
{
    private $describeReflection = null;

    function describeClass()
    {
        return [
            'title' => $this->describeTitle($this->describeGetComment()),
            'description' => $this->describeDescription($this->describeGetComment())
        ];
    }

    function describeActions($prefix = 'action')
    {
        $actions = [];
        foreach ($this->describeGetReflection()->getMethods() as $method) {
            if (Str :: pos($prefix, $method->name) === 0 && $method->isPublic() && !$method->isStatic()) {
                $name = Str :: snake(substr($method->name, strlen($prefix)));
                $actions[$name] = [
                    'title' => $this->describeTitle($method->getDocComment()),
                    'description' => $this->describeDescription($method->getDocComment()),
                    'tags' => $this->describeTags($method->getDocComment())
                ];
            }
        }
        return $actions;
    }

    private function describeGetReflection()
    {
        if (is_null($this->describeReflection)) {
            $this->describeReflection = new \ReflectionClass($this);
        }
        return $this->describeReflection;
    }

    private function describeGetComment()
    {
        return $this->describeGetReflection()->getDocComment();
    }

    private function describeTitle($comment)
    {
        $docLines = preg_split('~\R~u', $comment);
        if (isset($docLines[1])) {
            return trim($docLines[1], "\t *");
        }
        return '';
    }

    private function describeDescription($comment)
    {
        $comment = strtr(trim(preg_replace('/^\s*\**( |\t)?/m', '', trim($comment, '/'))), "\r", '');
        if (preg_match('/^\s*@\w+/m', $comment, $matches, PREG_OFFSET_CAPTURE)) {
            $comment = trim(substr($comment, 0, $matches[0][1]));
        }
        return $comment;
    }

    private function describeTags($comment)
    {
        $comment = "@description \n" . strtr(trim(preg_replace('/^\s*\**( |\t)?/m', '', trim($comment, '/'))), "\r", '');
        $parts = preg_split('/^\s*@/m', $comment, -1, PREG_SPLIT_NO_EMPTY);
        $tags = [];
        foreach ($parts as $part) {
            if (preg_match('/^(\w+)(.*)/ms', trim($part), $matches)) {
                $name = $matches[1];
                if (!isset($tags[$name])) {
                    $tags[$name] = trim($matches[2]);
                } elseif (is_array($tags[$name])) {
                    $tags[$name][] = trim($matches[2]);
                } else {
                    $tags[$name] = [$tags[$name], trim($matches[2])];
                }
            }
        }
        return $tags;
    }

}
<?php


namespace App;


use Symfony\Component\HttpFoundation\Request;

class JsonRequestHandler
{
    private $bodyJson;

    function __construct(Request $request)
    {
        $this->bodyJson = json_decode($request->getContent(), true);
    }

    public function getString($key): ?string
    {
        if (isset($this->bodyJson[$key])) {
            return $this->bodyJson[$key];
        } else {
            return null;
        }
    }
    public function getArray($key){
        return $this->bodyJson[$key];
    }
}
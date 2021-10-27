<?php


namespace App;


use Doctrine\Common\Collections\ArrayCollection;

class AuthenticationResponse implements \JsonSerializable
{
    private ArrayCollection $errors;
    private ?AuthenticationData $data;

    /**
     * AuthenticationResponse constructor.
     * @param ArrayCollection $errors
     * @param ?AuthenticationData $data
     */
    public function __construct(ArrayCollection $errors, ?AuthenticationData $data)
    {
        $this->errors = $errors;
        $this->data = $data;
    }


    public function jsonSerialize()
    {
        return [
           'errors' => $this->errors,
            'data'=>$this->data
        ];
    }
}

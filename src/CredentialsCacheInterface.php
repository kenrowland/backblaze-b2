<?php


namespace BackblazeB2;


interface CredentialsCacheInterface
{
    /**
     * @param $values
     * @return mixed - TRUE if saved, FALSE otherwise
     */
    public function put($values);

    /**
     * @return mixed - FALSE if nothing stored, credentials otherwise
     */
    public function get();
}
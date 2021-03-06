<?php

namespace App\Manager;

use Symfony\Component\HttpFoundation\Session\Session;

class SessionManager
{
    public const IDS_COMMANDE = 'IdsRecapCommande';
    private $session;
    /**
     * to init session 
     */
    public function initSession()
    {
        $this->session = new Session();
    }
    /**
     * get a key if exist
     */
    public function get($key)
    {
        return $this->session->get($key);
    }

    public function remove($key)
    {
        $this->session->remove($key);
    }

    /**
     * to set content array 
     */
    public function addArraySession($key, $array)
    {
        if ($this->session->has($key)) {
            $paramSession = array_unique(array_merge($this->get($key), $array));
            $this->session->remove($key);
            $this->session->set($key, $paramSession);
        } else {
            //create new session if is not exist
            $this->session->set($key, $array);
        }
    }

}
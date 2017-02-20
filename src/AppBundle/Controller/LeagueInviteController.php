<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LeagueInviteController extends Controller
{
    public $inviteSalt;
    public function leagueHash($id,$email)
    {
        $key = $id.$email.$this->inviteSalt;
        return md5($key);
    }
}
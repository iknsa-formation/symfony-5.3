<?php
/**
 * Created by iKNSA.
 * User: Khalid Sookia <khalidsookia@gmail.com>
 * Date: 10/06/2021
 * Time: 16:37
 */


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AppController extends AbstractController
{
    public function home()
    {
        return $this->render('app/home.html.twig');
    }

    public function contact()
    {
        return $this->render('app/contact.html.twig');
    }
}

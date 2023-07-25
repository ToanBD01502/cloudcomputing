<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\SanPham;
use App\Entity\Category;
use App\Form\SanPhamType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Annotation\Route;

class SanPhamController extends AbstractController
{
    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }
    #[Route('/san/pham', name: 'app_san_pham')]
    public function index(EntityManagerInterface $em, Request $req, FileUploader $fileUploader): Response
    {
        $sp = new SanPham();
        $form = $this->createForm(SanPhamType::class, $sp);
        $form->handleRequest($req);

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $file = $form->get("photo")->getData();
            $fileName = $fileUploader->upload($file);
            $data->setPhoto($fileName);

            $em->persist($data);
            $em->flush();
            return new RedirectResponse($this->urlGenerator->generate('app_ds_san_pham'));
        }

        return $this->render('san_pham/index.html.twig', [
            'sp_form' => $form->createView(),
        ]);
    }
}

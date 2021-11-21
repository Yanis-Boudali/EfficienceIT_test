<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class ContactController extends AbstractController
{
    /**
     * @Route("/", name="contact_index", methods={"GET"})
     */
    public function index(ContactRepository $contactRepository): Response
    {
        return $this->render('contact/index.html.twig', [
            'contacts' => $contactRepository->indexDesc(),
        ]);
    }

    /**
     * @Route("/new", name="contact_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager, \Swift_Mailer $mailer): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contact = $form->getData();

            // Préparation envoi de mails
            $expediteur = $contact->getEmail();
            $destinataire = $contact->getDepartement()->getEmail();
            $contentType = 'text/plain';
            $charset = 'utf-8';
            $subject = 'Contact';
            $body = $contact->getMessage();

            // On crée le message
            $message = (new \Swift_Message(null))
                // On attribue l'expéditeur
                ->setFrom($expediteur)
                // On attribue le destinataire
                ->setTo($destinataire)
                // On crée le sujet
                ->setSubject($subject)
                // On crée le texte avec la vue
                ->setBody($body, $contentType, $charset);
            $mailer->send($message);

            $this->addFlash('message', 'Votre message a été transmis à'.$contact->getDepartement()->getEmail().', nous vous répondrons dans les meilleurs délais.'); // Permet un message flash de renvoi
            
            $entityManager->persist($contact);
            $entityManager->flush();

            return $this->redirectToRoute('contact_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('contact/new.html.twig', [
            'contact' => $contact,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="contact_show", methods={"GET"})
     */
    public function show(Contact $contact): Response
    {
        return $this->render('contact/show.html.twig', [
            'contact' => $contact,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="contact_edit", methods={"GET", "POST"})
     */
    // public function edit(Request $request, Contact $contact, EntityManagerInterface $entityManager): Response
    // {
    //     $form = $this->createForm(ContactType::class, $contact);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $entityManager->flush();

    //         return $this->redirectToRoute('contact_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->render('contact/edit.html.twig', [
    //         'contact' => $contact,
    //         'form' => $form->createView(),
    //     ]);
    // }

    /**
     * @Route("/{id}", name="contact_delete", methods={"POST"})
     */
    public function delete(Request $request, Contact $contact, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$contact->getId(), $request->request->get('_token'))) {
            $entityManager->remove($contact);
            $entityManager->flush();
        }

        return $this->redirectToRoute('contact_index', [], Response::HTTP_SEE_OTHER);
    }
}

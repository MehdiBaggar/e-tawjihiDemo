<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Entity\Profile;
use App\Entity\UserMetaData;
use App\Form\PersonneType;
use App\Repository\PersonneRepository;
use App\Repository\UserRepository;
use App\Service\UploaderService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;



final class PersonneController extends AbstractController
{

    public function __construct(private UserRepository $userRepository,private PersonneRepository $personneRepository,)
    {
        
    }

    #[Route('/personne',name:'personne.list')]
    public function index(ManagerRegistry $doctrine):Response{

       $repository = $doctrine->getRepository(Personne::class);
       $personnes = $repository->findAll();
       return $this->render('personne/index.html.twig',
       [
           'personnes'=>$personnes
       ]);
   }
    #[Route('/personne/{id<\d+>}', name: 'personne.detail')]
    public function detail(/*ManagerRegistry $doctrine, */Personne $personne=null): Response
    {
        //$repository = $doctrine->getRepository(Personne::class);
        //$personne = $repository->find($id);

        // Vérifiez si la personne existe
        if (!$personne) {
            $this->addFlash("error", "La personne n'existe pas.");
            // Redirigez et arrêtez l'exécution du contrôleur
            return $this->redirectToRoute('personne.list');
        }

        // Rendre le template seulement si la personne existe
        return $this->render('personne/personne.html.twig', [
            'personne' => $personne,
        ]);
    }
    #[Route('/personne/Alls/{page?1}/{nbrE?10}',name:'personne.Alls'),
    IsGranted('ROLE_USER')]
    public function indexAlls(ManagerRegistry $doctrine,$page,$nbrE):Response{

        $repository = $doctrine->getRepository(Personne::class);
        $personnes = $repository->findBy([],[],$nbrE,($page -1)*$nbrE);
        $nbrPersonnes = $repository->count([]);
        $nbrPages = ceil($nbrPersonnes/$nbrE);

        // page 1
        return $this->render('personne/index.html.twig',
            [
                'personnes'=>$personnes,
                'isPaginated'=>true,
                'nbrPages'=>$nbrPages,
                'page'=>$page,
                'nbrE'=>$nbrE
            ]);
    }


    #[Route('/personne/edit/{id?0}', name: 'personne.edit')]
    public function editPersonne(Personne $personne=null, ManagerRegistry $doctrine,Request $request,UploaderService $uploaderService,
                                 #[Autowire('%kernel.project_dir%/public/uploads/personnes')] string $personnes_Directory): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        //initialisation du 'entity Manager'
        $entityManager = $doctrine->getManager();
        $new = false;
        if (!$personne){
            $personne = new Personne();
            $new = true;
        }

        $form = $this->createForm(PersonneType::class,$personne);
        $form->remove('createdAt');
        $form->remove('updatedAt');
        $form->handleRequest($request);
        //si le dormulaire a ete remplie :
        if($form->isSubmitted()&&$form->isValid() ) {
            //on va ajouter l'objet personne a la base de données
            /** @var UploadedFile $brochureFile */
            $photo = $form->get('photo')->getData();
            if ($photo) {
                $newFilename=$uploaderService->uploadFile($photo,$personnes_Directory);
                $personne->setImage($newFilename);
            }
            $manager = $doctrine->getManager();
            $manager->persist($personne);
            $manager->flush();
            //afficher un message de succes
            if ($new){
                $this->addFlash('success','la personne a ete ajouter avec succes !');
            }
            $this->addFlash('success','la personne a ete modifiée avec succes !');
            // redirecter vers la page alls
            return $this->redirectToRoute('personne.Alls');
        }
        //sinon
        else {
            //afficher un message d'error
            $this->addFlash('error','merci de bien remplie le formulaire svp !');
            //afficher rediger vers la page de formulaire
            return $this->render('personne/add-personne.html.twig', [
                'form'=>$form->createView()
            ]);
        }

    }
    #[Route('/personne/delete/{id<\d+>}', name: 'personne.delete')]
    public function deletePersonne(ManagerRegistry $doctrine, Personne $personne=null): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Vérifiez si la personne existe
        if ($personne) {
            $manager = $doctrine->getManager();
            $manager->remove($personne);
            $manager->flush();
            $this->addFlash("success", "La personne a été suprimmé avec succés !.");

        }
        else{
            $this->addFlash("error","la personne n'existe pas !");

        }
        return $this->redirectToRoute('personne.Alls');
    }
    #[Route('/personne/update/{id<\d+>}/{firstName}/{name}/{age}')]
    public function updatePersone(ManagerRegistry $doctrine, Personne $personne=null,$firstName,$name,$age):RedirectResponse

    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        //si la personne existe :
        if ($personne){
            //initialiser le manager :
            $manager=$doctrine->getManager();
            //modification du personne :
            $personne->setFirstName($firstName);
            $personne->setName($name);
            $personne->setAge($age);
            //la persistance :
            $manager->persist($personne);
            $manager->flush();
            //aficher un message de succés :
            $this->addFlash("success","la personne a été modifié avec succés !");
        }
        else{
            //si la personne n'existe pas on va afficher un message d'erreur :
            $this->addFlash("error","la personne n'existe pas !");
        }
        return $this->redirectToRoute('personne.Alls');
    }
    #[Route('/personne/Alls/age/{ageMin}/{ageMax}',name:'personne.byAge')]
    public function personnesByAge(ManagerRegistry $doctrine,$ageMin,$ageMax):Response{

        $repository = $doctrine->getRepository(Personne::class);
        $personnes = $repository->findByAgeInterval($ageMin,$ageMax);
        return $this->render('personne/index.html.twig',
            [
                'personnes'=>$personnes
            ]);
    }
    #[Route('/personne/Stats/age/{ageMin}/{ageMax}',name:'personne.byAge')]
    public function statsPersonnesByAge(ManagerRegistry $doctrine,$ageMin,$ageMax):Response{

        $repository = $doctrine->getRepository(Personne::class);
        $stats = $repository->statsByAgeInterval($ageMin,$ageMax);
        return $this->render('personne/stats.html.twig',
            [
                'stats'=>$stats[0]
            ]);
    }
    #[Route('/personne/setProfile',name:'personne.setProfile')]
    public function setProfile(ManagerRegistry $doctrine):Response{

        $manager = $doctrine->getManager();

        $profile4 = new Profile();
        $profile4 -> setRs('google');
        $profile4 ->setUrl('https://www.google.com');

        $manager->persist($profile4);
        $manager->flush();
        return new Response("<h1> le profile a ete ajoutée</h1>");
    }
    #[Route('/', name: 'personnes.vue')]
    public function getPersonnesToVue(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Personne::class);
        $personnes = $repository->findAll();

        // Convertir les objets en un tableau de données simple
        $personnesArray = array_map(function ($personne) {
            return [
                'id' => $personne->getId(),
                'firstName' => $personne->getFirstName(),
                'name' => $personne->getName(),
                'age' => $personne->getAge()
            ];
        }, $personnes);

        return $this->render('personne/personneVue.html.twig', [
            'personnes' => $personnesArray // JSON correctement formaté
        ]);
    }
    #[Route('/fields/{id}', name: 'personnes.fields')]
    public function about($id): Response
    {
        return $this->render('personne/fields.html.twig');    }

    #[Route('/api/personnes', name: 'personnes.api.personne',methods: ['GET'])]
    public function apiPersonne(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Personne::class);
        $personnes = $repository->findAll();

        // Convertir les objets en un tableau de données simple
        $personnesData = array_map(function ($personne) {
            return [
                'id' => $personne->getId(),
                'firstName' => $personne->getFirstName(),
                'name' => $personne->getName(),
                'age' => $personne->getAge()
            ];
        }, $personnes);
        return $this->json($personnesData, Response::HTTP_OK, []);
    }

    #[Route('/api/metadata', name: 'add_personne_metadata', methods: ['POST'])]
    public function addUserMetadata(Request $request, ManagerRegistry $doctrine): Response
    {
        $manager = $doctrine->getManager();
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse(['status' => 'error', 'message' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        $id = $data['id'] ?? null; // Extract ID from request body

        if (!$id) {
            return new JsonResponse(['status' => 'error', 'message' => 'Missing user ID'], Response::HTTP_BAD_REQUEST);
        }
        dump('Received ID:', $id); // Debug ID in Symfony logs
        file_put_contents('/tmp/id_debug.log', 'Received ID: ' . $id . PHP_EOL, FILE_APPEND);



        $personne = $manager->getRepository(Personne::class)->find($id);

        if (!$personne) {
            return new JsonResponse(["status" => "error", "message" => "Person not found"], 404);
        }

        $fieldName = $data['fieldName'] ?? null;
        $fieldType = $data['fieldType'] ?? null;
        $fieldValue = $data['fieldValue'] ?? null;

        if (!$fieldName || !$fieldType || !$fieldValue) {
            return new JsonResponse(['status' => 'error', 'message' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        $userMetaData = new UserMetaData();
        $userMetaData->setPersonne($personne);
        $userMetaData->setFieldName($fieldName);
        $userMetaData->setFieldType($fieldType);
        $userMetaData->setFieldValue($fieldValue);

        $manager->persist($userMetaData);
        $manager->flush();

        return new JsonResponse(['status' => 'success', 'message' => 'User metadata added successfully!'], Response::HTTP_OK);
    }
    #[Route('/api/check-role', name: 'api_check_role', methods: ['GET'])]
    public function checkRoles(): JsonResponse
    {

        $isAdmin = $this->isGranted('ROLE_ADMIN');

        return new JsonResponse([
            'isAdmin' => $isAdmin,
        ]);
    }

    #[Route('/contrat', name: 'personne.contrat')]
    public function generatePdf(): Response
    {
        return $this->render('personne/contrat.html.twig');
    }





}

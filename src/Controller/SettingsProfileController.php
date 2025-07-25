<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserProfile;
use App\Form\ProfilePictureForm;
use App\Form\UserProfileForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

final class SettingsProfileController extends AbstractController
{
    #[Route('/settings/profile', name: 'app_settings_profile')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function profile(EntityManagerInterface $entityManager, Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $userProfile = $user->getUserProfile() ?? new UserProfile();

        $form = $this->createForm(UserProfileForm::class, $userProfile);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userProfile = $form->getData();
            $user->setUserProfile($userProfile);
            $entityManager->persist($userProfile);
            $entityManager->flush();

            $this->addFlash('success', 'User profile succesfully saved');

            return $this->redirectToRoute('app_settings_profile');
        }

        return $this->render('settings_profile/profile.html.twig', [
            'form' => $form,
        ]);
    }


    #[Route('/settings/profile/picture', name: 'app_settings_profile_picture')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function profilePicture(EntityManagerInterface $entityManager, SluggerInterface $slugger, Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(ProfilePictureForm::class);

        $form->handleRequest($request);

        $userProfile = $user->getUserProfile() ?? new UserProfile();

        // Check if the user has a profile picture and return it if it exists
        $currentProfilePicture = $userProfile->getImage() ? $this->getParameter('profile_picture_directory').'/'.$userProfile->getImage() : null;

        if ($form->isSubmitted() && $form->isValid()) {
            $profilePicture = $form->get('profilePicture')->getData();

            if($profilePicture){
                $originalFileName = pathinfo($profilePicture->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $slugger->slug($originalFileName).'-'.uniqid().'.'.$profilePicture->guessExtension();

                try{
                    $profilePicture->move(
                        $this->getParameter('profile_picture_directory'),
                        $newFilename
                    );
                } catch (\Exception $e) {
                    $this->addFlash('error', 'There was an error uploading your profile picture');
                }
            }

            $userProfile->setImage($newFilename);

            $entityManager->persist($userProfile);
            $entityManager->flush();

            $this->addFlash('success', 'Profile picture succesfully updated');

            return $this->redirectToRoute('app_settings_profile_picture');
        }

        return $this->render('settings_profile/profile_picture.html.twig', [
            'form' => $form,
            'profile' => $userProfile,
        ]);
    }
}

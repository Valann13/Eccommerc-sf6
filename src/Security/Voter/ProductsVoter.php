<?php

namespace App\Security\Voter;

use App\Entity\Products;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class ProductsVoter extends Voter  {

    const EDIT = 'PRODUCT_EDIT';
    const DELETE = 'PRODUCT_DELETE';

    private $security;

    public function __construct(Security $security){
        $this->security = $security;
    }

    protected function supports(string $attribute, $product):bool{
        if(!in_array($attribute,[self::EDIT, self::DELETE])){
            return false;
        }
        if(!$product instanceOf Products){
            return false;
        }
        return true;

        // Autre façon d'écrire tout ça :
        // return in_array($attribute,[self::EDIT, self::DELETE]) && $product instanceOf Products
    }

    protected function voteOnAttribute($attribute, $product, TokenInterface $token):bool{
        // Récupération user à partir du token
        $user = $token->getUser();

        // Est ce que l'utilisateur est connecté
        if(!$user instanceof UserInterface){
            return false;
        }

        // Est ce que l'utilisateur est admin
        if($this->security->isGranted('ROLE_ADMIN')){
            return true;
        }

        // on vérife ses permissions si il n'est pas admin
        switch($attribute){
            case self::EDIT:
                // on verifie si l'utilisateur peux editer
                return $this->canEdit();
                break;
            case self::DELETE:
                //on verifie si l'utilsateur peux supprimer
                return $this->canDelete();
                break;
        }

    }
    private function canEdit(){
        return $this->security->isGranted('ROLE_PRODUCT_ADMIN');
    }
    private function canDelete(){
        return $this->security->isGranted('ROLE_PRODUCT_ADMIN');
    }


}
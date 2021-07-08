<?php

namespace App\Form;

use App\Entity\Address;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use function Sodium\add;

class UserAddressListType extends AbstractType
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $security = $this->security;
        $builder
            ->add('deliveryAddress', EntityType::class, [
                'class' => Address::class,
                'query_builder' => function (EntityRepository $entityRepository) use ($security) {
                    return $entityRepository->createQueryBuilder('a')
                        ->where('a.user = ' . $security->getUser()->getId());
                },
                'choice_label' => function (Address $address) {
                    return $address->getNumber() . ' ' . $address->getStreet()
                        . $address-> getZip();
                }
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-success'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}

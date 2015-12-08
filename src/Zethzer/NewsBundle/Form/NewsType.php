<?php

namespace Zethzer\NewsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NewsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre', 'text')
            ->add('contenu', 'textarea')
            ->add('type', 'choice', array('choices' => array('0' => 'Normale',
                                                             '1' => 'Slide',
                                                             '2' => 'Important'),
                                          'expanded' => true,
                ))
            ->add('image', new ImageType())
            ->add('categories', 'entity', array('class'    => 'ZethzerNewsBundle:Categorie',
                                                'property' => 'nom',
                                                'multiple' => true,
                                                'required' => false))
            ->add('addCategories', 'collection', array('type' => new CategorieType(),
                                                    'allow_add' => true,
                                                    'allow_delete' => true,
                                                    'by_reference' => false,
                                                    'required' => false,
                                                    'mapped' => false));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Zethzer\NewsBundle\Entity\News'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'zethzer_newsbundle_news';
    }
}

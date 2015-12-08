<?php

namespace Zethzer\NewsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContactType extends AbstractType
{
	/**
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
       		->add('email', 'email')
			->add('subject', 'text')
			->add('content', 'textarea');
	}

	/**
	 * @param OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
            'data_class' => 'Zethzer\NewsBundle\Form\Model\Contact'
        ));
	}

	public function getName()
	{
		return 'zethzer_newsbundle_contact';
	}
}

?>
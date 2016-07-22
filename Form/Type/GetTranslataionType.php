<?php

namespace Maith\Common\TranslatorBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Description of GetTranslataionType
 *
 * @author Rodrigo Santellan
 */
class GetTranslataionType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
      $builder
          ->add('bundle', 'choice', array(
                'choices' => $options['bundles'],
          ))
          ->add('lang', 'choice', array(
               'choices' => $options['langs'],
          ));
  }
 
  /**
   * @param OptionsResolverInterface $resolver
   */
  public function configureOptions(OptionsResolver $resolver)
  {
      $resolver->setDefaults(array(
          'bundles' => array(),
          'langs' => array(),
      ));
  }
    
  public function getName() {
    return 'maith_translator_get_type';
  }

}

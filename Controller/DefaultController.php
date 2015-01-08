<?php

namespace Maith\Common\TranslatorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Finder\Finder;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $admin = $this->getRequest()->get('admin');
        
        $list = $this->container->getParameter('translation_bundles');
        if(!is_null($admin))
        {
          $list = array_merge($list, $this->container->getParameter('admin_translation_bundles'));
        }
        $lang_list = $this->container->getParameter('translation_languages');;
        //$path = $this->get('kernel')->getBundle('LoopitaMetalizadoraBundle')->getPath();
        return $this->render('MaithCommonTranslatorBundle:Default:index.html.twig', array('bundles' => $list, 'langs' => $lang_list));
    }
    
    public function getTranslationAction(){
        
        $bundle = $this->getRequest()->get("bundle");
        $lang = $this->getRequest()->get("lang");
        $path = $this->get('kernel')->getBundle($bundle)->getPath()."/Resources/translations";
        $loader = $this->get('translation.loader');
        $catalog = new MessageCatalogue($lang);
        $response = new JsonResponse();
        try
        {
          $loader->loadMessages($path, $catalog);
          $response->setData(array('status'=> 'OK', 'options' => array('html' => $this->renderView("MaithCommonTranslatorBundle:Default:showLangKeysValues.html.twig", array('bundle' => $bundle, 'lang' => $lang, 'translations' => $catalog->all("messages"))) )));
        }
        catch(\Exception $e)
        {
          $response->setData(array('status' => 'ERROR', 'options' => array('message' => $e->getMessage(), 'code' => $e->getCode())));
        }
        return $response;
    }
    

    public function setTranslationAction(){
        
        $bundle = $this->getRequest()->get("bundle");
        $lang = $this->getRequest()->get("lang");
        $key = $this->getRequest()->get("key");
        $value = $this->getRequest()->get("value");
        $path = $this->get('kernel')->getBundle($bundle)->getPath()."/Resources/translations";
        $loader = $this->get('translation.loader');
        $catalog = new MessageCatalogue($lang);
        $loader->loadMessages($path, $catalog);
        $response = new JsonResponse();
        $messages_list = $catalog->all("messages");
        if(!isset($messages_list[$key]))
        {
          throw new NotFoundHttpException("Key not found");
        }
        $messages_list[$key] = $value;
        
        $catalog->replace($messages_list, 'messages');
        $writer = $this->get('translation.writer');
        $writer->writeTranslations($catalog, 'xlf', array('path' => $path));
        
        $response->setData(array('status' => 'OK'));
        return $response;
    }
    
    public function clearTranslationCacheAction(){
      
      $cacheDir = $this->get('kernel')->getRootDir().DIRECTORY_SEPARATOR."cache";
      $finder = new Finder();
      $finder->files()->in($cacheDir)->name("catalogue*");
      foreach($finder as $file)
      {
         @unlink($file->getRealpath());
      }
      $finderTwig = new Finder();
      $finderTwig->files()->in($cacheDir.DIRECTORY_SEPARATOR."*".DIRECTORY_SEPARATOR."twig");
      foreach($finderTwig as $file)
      {
         @unlink($file->getRealpath());
      }
      try
      {
        $finderHttpCache = new Finder();
        $finderHttpCache->files()->in($cacheDir.DIRECTORY_SEPARATOR."*".DIRECTORY_SEPARATOR."http_cache");
        foreach($finderHttpCache as $file)
        {
           @unlink($file->getRealpath());
        }
      }catch(\Exception $e)
      {
        
      }
      $response = new JsonResponse();
      $response->setData(array('status' => 'OK'));
      return $response;
    }
}

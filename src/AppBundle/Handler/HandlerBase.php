<?php
namespace AppBundle\Handler;

use AppBundle\Exception\InvalidFormException;
use AppBundle\Repository\MerchantRepository;
use AppBundle\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Form\FormFactoryInterface;

abstract class HandlerBase
{
    const LIMIT = 20;

    protected $entityManager;
    protected $formFactory;
    protected $formType;

    /**
     * @var Paginator
     */
    protected $knpPaginator;

    /**
     * @var string
     */
    protected $dirRoot;

    public function __construct(EntityManager $entityManager,
                                FormFactoryInterface $formFactory)
    {
        $this->entityManager = $entityManager;
        $this->formFactory   = $formFactory;
    }

    protected function calcOffset($page, $limit = self::LIMIT)
    {
        if (null == $page)
            return null;

        return (abs($page) - 1) * $limit;
    }


    public function setKnpPaginator(Paginator $knpPaginator)
    {
        $this->knpPaginator = $knpPaginator;
    }

    /**
     * @param string $dirRoot
     */
    public function setDirRoot($dirRoot)
    {
        $this->dirRoot = $dirRoot;
    }

    /**
     * @return string
     */
    public function getDirRoot()
    {
        return $this->dirRoot;
    }

    // region REST

    abstract public function get($id);

    public function post($entity, $parameters)
    {
        return $this->processForm($entity, $parameters, 'POST');
    }

    public function put($entity, $parameters)
    {
        return $this->processForm($entity, $parameters, 'PUT');
    }

    public function patch($entity, $parameters)
    {
        return $this->processForm($entity, $parameters, 'PATCH');
    }

    public function delete($entity)
    {
        $this->entityManager->persist($entity->setDeleted(true));
        $this->entityManager->flush($entity);

        return $entity->isDeleted();
    }

    /**
     * @param $entity
     *
     * @return mixed
     */
    public function save($entity)
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush($entity);

        return $entity;
    }

    protected function processForm($entity, $parameters, $method, $persisted = true)
    {
        $form = $this->formFactory->create($this->formType, $entity, ['csrf_protection' => false]);
        $form->submit($parameters, 'PATCH' !== $method);

        if (!$form->isValid())
            throw new InvalidFormException("Invalid Submit", $form);

        $data = $form->getData();

        if ($persisted) {
            $this->entityManager->persist($data);
            $this->entityManager->flush();
        }

        return $data;
    }

    // endregion

    // region Repository

    /**
     * @return MerchantRepository
     */
    protected function getMerchantRepository()
    {
        return $this->entityManager->getRepository('AppBundle:Merchant');
    }

    /**
     * @return UserRepository
     */
    protected function getUserRepository()
    {
        return $this->entityManager->getRepository('AppBundle:User');
    }

    // endregion
}
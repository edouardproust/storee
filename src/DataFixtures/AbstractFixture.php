<?php

namespace App\DataFixtures;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

abstract class AbstractFixture extends Fixture implements ContainerAwareInterface, FixtureInterface
{
    protected $container;

    abstract protected function envLoad(ObjectManager $manager);
    
    abstract protected function getEnvironments();

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
    
        $kernel = $this->container->get('kernel');

        if (in_array($kernel->getEnvironment(), $this->getEnvironments())) {
            $this->envLoad($manager);
        }
    }

}
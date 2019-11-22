<?php

namespace Oro\Bundle\DataGridBundle\Entity\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Oro\Bundle\DataGridBundle\Entity\AppearanceType;

class AppearanceTypeManager
{
    /** @var EntityManagerInterface */
    protected $em;

    /** @var  array */
    protected $appearanceTypes;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Get appearance types
     *
     * @return array
     */
    public function getAppearanceTypes()
    {
        if (null === $this->appearanceTypes) {
            $this->appearanceTypes = [];
            $types = $this->em->getRepository('OroDataGridBundle:AppearanceType')->findAll();
            /** @var  $type AppearanceType */
            foreach ($types as $type) {
                $this->appearanceTypes[$type->getName()] = [
                    'label' => $type->getLabel(),
                    'icon'  => $type->getIcon()
                ];
            }
        }

        return $this->appearanceTypes;
    }
}

<?php

namespace Oro\Bundle\DataGridBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="Oro\Bundle\DataGridBundle\Entity\Repository\GridViewRepository")
 * @UniqueEntity(
 *      fields={"name", "gridName"},
 *      message="oro.datagrid.gridview.unique"
 * )
 */
class GridView extends AbstractGridView
{
}

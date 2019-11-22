<?php

namespace Oro\Bundle\DataGridBundle\Command;

use Doctrine\ORM\Query\Parameter;
use Oro\Bundle\DataGridBundle\Datasource\Orm\OrmDatasource;
use Oro\Bundle\DataGridBundle\Event\OrmResultAfter;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProfileDatagridCommand extends AbstractDatagridDebugCommand
{
    const NAME = 'gorgo:profile:datagrid';

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
//        if (!$input->getOption('current-user')) {
//            $output->writeln(sprintf('Option "%s" required', 'current-user'));
//
//            return 1;
//        }
//
//        if (!$input->getOption('current-organization')) {
//            $output->writeln(sprintf('Option "%s" required', 'current-organization'));
//
//            return 1;
//        }

        $eventDispatcher = $this->getContainer()->get('event_dispatcher');
        $queryData = [];
        $eventDispatcher->addListener(OrmResultAfter::NAME, function (OrmResultAfter $event) use (&$queryData) {
            $datagrid = $event->getDatagrid();
            if ($datagrid->getDatasource() instanceof OrmDatasource) {
                $query = $event->getQuery();
                $queryData[$datagrid->getName()][] = [
                    'sql' => $query->getSQL(),
                    'parameters' => $query->getParameters()->toArray(),
                ];
            }
        });

        $datagridName = $input->getArgument('datagrid');
        $parameters = $this->parseJsonOption($input->getOption('bind'));
        $additionalParameters = $this->parseJsonOption(json_encode([
            '_pager' => [
                '_page' => 2,
                '_per_page' => 2,
            ]
        ]));

        $datagridManager = $this->getContainer()->get('oro_datagrid.datagrid.manager');
        $translator = $this->getContainer()->get('translator');

        $datagrid = $datagridManager->getDatagrid($datagridName, $parameters, $additionalParameters);
        $config = $datagrid->getConfig();
        $columns = $config->offsetGet('columns');
        $headers = [];
        foreach ($columns as $column => $options) {
            $headers[$column] = $options['translatable'] ? $translator->trans($options['label']) : $options['label'];
        }
        $table = new Table($output);
        $table->setHeaders($headers);
        $data = $datagrid->getData()->toArray();
        foreach ($data['data'] as $item) {
            $row = [];
            foreach ($headers as $column => $title) {
                if (is_array($item[$column])) {
                    $first = reset($item[$column]);
                    if (is_array($first)) {
                        $row[$column] = 'ArrayData';//json_encode($item[$column]);
                    } else {
                        $row[$column] = implode(',', $item[$column]);
                    }
                    continue;
                }
                $row[$column] = $item[$column];
            }
            $table->addRow($row);
        }
        $table->render();

        if (!empty($queryData[$datagridName])) {
            $output->writeln('');
            $output->writeln('SQL Query:');
            $output->writeln($queryData[$datagridName][0]['sql']);
            $output->writeln('');
            $parameters = $queryData[$datagridName][0]['parameters'];
            if (count($parameters)) {
                $output->writeln('SQL Parameters:');
                $table = new Table($output);
                $table->setHeaders([
                    'name',
                    'value',
                    'type',
                ]);
                /** @var Parameter $parameter */
                foreach ($parameters as $parameter) {
                    $value = $parameter->getValue();
                    $table->addRow([
                        $parameter->getName(),
                        is_array($value) ? implode(',', $value) : $value,
                        $parameter->getType(),
                    ]);
                }
                $table->render();
            }
        }
    }
}

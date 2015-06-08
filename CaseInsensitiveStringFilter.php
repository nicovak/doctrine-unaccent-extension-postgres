<?php

namespace Acme\DemoBundle\Admin\Filter;

use Sonata\AdminBundle\Form\Type\Filter\ChoiceType;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\DoctrineORMAdminBundle\Filter\StringFilter;

class CaseInsensitiveStringFilter extends StringFilter
{
    /**
     * {@inheritdoc}
     */
    public function filter(ProxyQueryInterface $queryBuilder, $alias, $field, $data)
    {
        if (!$data || !is_array($data) || !array_key_exists('value', $data)) {
            return;
        }

        $data['value'] = trim($data['value']);

        if (strlen($data['value']) == 0) {
            return;
        }

        $data['type'] = !isset($data['type']) ? ChoiceType::TYPE_CONTAINS : $data['type'];

        $operator = $this->getOperator((int)$data['type']);

        if (!$operator) {
            $operator = 'LIKE';
        }

        $parameterName = $this->getNewParameterName($queryBuilder);

        $this->applyWhere($queryBuilder, sprintf('LOWER(UNACCENT(%s.%s)) %s :%s', $alias, $field, $operator, $parameterName));

        if ($data['type'] == ChoiceType::TYPE_EQUAL) {
            $queryBuilder->setParameter($parameterName, $this->handleParameter($data['value']));
        } else {
            $queryBuilder->setParameter($parameterName, sprintf($this->getOption('format'), $this->handleParameter($data['value'])));
        }

    }

    private function getOperator($type)
    {
        $choices = array(
            ChoiceType::TYPE_CONTAINS => 'LIKE',
            ChoiceType::TYPE_NOT_CONTAINS => 'NOT LIKE',
            ChoiceType::TYPE_EQUAL => '=',
        );

        return isset($choices[$type]) ? $choices[$type] : false;
    }

    private function handleParameter($parameter)
    {
        $parameter = htmlentities($parameter, ENT_NOQUOTES, 'utf-8');
        $parameter = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $parameter);
        $parameter = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $parameter);
        $parameter = preg_replace('#&[^;]+;#', '', $parameter);
        return strtolower($parameter);
    }
}
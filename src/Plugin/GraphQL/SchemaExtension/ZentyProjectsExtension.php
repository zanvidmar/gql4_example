<?php

namespace Drupal\zenty_projects\Plugin\GraphQL\SchemaExtension;

use Drupal\graphql\GraphQL\ResolverBuilder;
use Drupal\graphql\GraphQL\ResolverRegistryInterface;
use Drupal\graphql\Plugin\GraphQL\SchemaExtension\SdlSchemaExtensionPluginBase;
use Drupal\zenty_projects\Plugin\GraphQL\QueryBase\Wrappers\QueryConnection;

/**
 * @SchemaExtension(
 *   id = "zenty_projects_extension",
 *   name = "Zenty Projects extension",
 *   description = "Zenty project entity fields",
 *   schema = "zenty_projects"
 * )
 */
class ZentyProjectsExtension extends SdlSchemaExtensionPluginBase {

  const gql_client = <<<GQL
    type Client {
      id: Int!
      name: String!
      author: String
      field_pid: String!
      field_codename: String
      field_description: String
    }
GQL;

  const gql_project = <<<GQL
    type Project {
      id: Int!
      name: String
      author: String
      field_pid: String!
      field_codename: String
      field_description: String
    }

    type Task {
      id: Int!
      name: String!
      author: String
      field_pid: String!
      field_codename: String
      field_description: String
    }

    type ClientsConnection {
      total: Int!
      items: [Client!]
    }

    type ProjectsConnection {
      total: Int!
      items: [Project!]
    }
GQL;

  public function getBaseDefinition() {
    return self::gql_client . self::gql_project;
  }

  public function getExtensionDefinition() {
    return <<<GQL
      extend type Query {
        client(id: Int!): Client
        project(id: Int!): Project
        task(id: Int!): Task
        clients: ClientsConnection
        projects: ProjectsConnection
      }
GQL;
  }

  /**
   * {@inheritdoc}
   */
  public function registerResolvers(ResolverRegistryInterface $registry) {
    $builder = new ResolverBuilder();

    $zenty_projects_bundles = [
      'client' => 'Client',
      'project' => 'Project',
      'task' => 'Task',
      'time_log' => 'TimeLog',
      'expense_log' => 'ExpenseLog',
      'invoice' => 'Invoice',
      'tag' => 'Tag'
    ];

    $registry->addFieldResolver('Query', 'clients',
      $builder->produce('query_clients')
    );

    $registry->addFieldResolver('Query', 'projects',
      $builder->produce('query_projects')
    );

    foreach ($zenty_projects_bundles as $key => $value) {
      $registry->addFieldResolver('Query', $key,
        $builder->produce('entity_load')
          ->map('type', $builder->fromValue('zenty_projects'))
          ->map('bundles', $builder->fromValue([$key]))
          ->map('id', $builder->fromArgument('id'))
      );

      $registry->addFieldResolver($value, 'id',
        $builder->produce('entity_id')
          ->map('entity', $builder->fromParent())
      );

      $registry->addFieldResolver($value, 'author',
        $builder->compose(
          $builder->produce('entity_owner')
            ->map('entity', $builder->fromParent()),
          $builder->produce('entity_label')
            ->map('entity', $builder->fromParent())
        )
      );

      $registry->addFieldResolver($value, 'name',
        $builder->compose(
          $builder->produce('entity_label')
            ->map('entity', $builder->fromParent())
        )
      );

      $registry->addFieldResolver($value, 'field_pid',
        $builder->compose(
          $builder->produce('property_path')
            ->map('type', $builder->fromValue('entity:zenty_projects'))
            ->map('value', $builder->fromParent())
            ->map('path', $builder->fromValue('field_pid.value'))
        )
      );

      $registry->addFieldResolver($value, 'field_description',
        $builder->compose(
          $builder->produce('property_path')
            ->map('type', $builder->fromValue('entity:zenty_projects'))
            ->map('value', $builder->fromParent())
            ->map('path', $builder->fromValue('field_description.value')),
          $builder->produce('convert_html_to_absolute_urls')
            ->map('string', $builder->fromParent())
        )
      );
    }

    $registry->addFieldResolver('Client', 'field_codename',
      $builder->compose(
        $builder->produce('property_path')
          ->map('type', $builder->fromValue('entity:zenty_projects'))
          ->map('value', $builder->fromParent())
          ->map('path', $builder->fromValue('field_codename.value'))
      )
    );

    $registry->addFieldResolver('Project', 'field_codename',
      $builder->compose(
        $builder->produce('property_path')
          ->map('type', $builder->fromValue('entity:zenty_projects'))
          ->map('value', $builder->fromParent())
          ->map('path', $builder->fromValue('field_codename.value'))
      )
    );

    $registry->addFieldResolver('ClientsConnection', 'total',
      $builder->callback(function (QueryConnection $connection) {
        return $connection->total();
      })
    );

    $registry->addFieldResolver('ClientsConnection', 'items',
      $builder->callback(function (QueryConnection $connection) {
        return $connection->items();
      })
    );

    $registry->addFieldResolver('ProjectsConnection', 'total',
      $builder->callback(function (QueryConnection $connection) {
        return $connection->total();
      })
    );

    $registry->addFieldResolver('ProjectsConnection', 'items',
      $builder->callback(function (QueryConnection $connection) {
        return $connection->items();
      })
    );
  }
}

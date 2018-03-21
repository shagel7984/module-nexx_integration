<?php

namespace Drupal\nexx_integration\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Utility\Token;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\nexx_integration\Controller\OmniaV2Controller;
use Drupal\nexx_integration\Controller\OmniaV3Controller;

/**
 * Class OmniaController.
 *
 * @package Drupal\nexx_integration\Controller
 */
class OmniaController extends ControllerBase {
  /**
   * The database service.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * The entity type bundle info service.
   *
   * @var \Drupal\Core\Entity\EntityTypeBundleInfo
   */
  protected $entityTypeBundleInfo;

  /**
   * The media entity.
   *
   * @var \Drupal\media_entity\MediaInterface
   */
  protected $mediaEntity;

  /**
   * The media entity storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $mediaEntityStorage;

  /**
   * The media entity definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeInterface
   */
  protected $mediaEntityDefinition;

  /**
   * The entity field manager.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * The logger service.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Token service.
   *
   * @var \Drupal\Core\Utility\Token
   */
  protected $token;

  /**
   * OmniaController constructor.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database service.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entity_type_bundle_info
   *   The entity type bundle info service.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager
   *   The entity field manager.
   * @param \Psr\Log\LoggerInterface $logger
   *   The logger service.
   * @param \Drupal\Core\Utility\Token $token
   *   Token service.
   */
  public function __construct(
    Connection $database,
    EntityTypeBundleInfoInterface $entity_type_bundle_info,
    EntityFieldManagerInterface $entity_field_manager,
    LoggerInterface $logger,
    Token $token
  ) {
    $this->database = $database;
    $this->entityTypeBundleInfo = $entity_type_bundle_info;
    $this->entityFieldManager = $entity_field_manager;
    $this->logger = $logger;
    $this->token = $token;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database'),
      $container->get('entity_type.bundle.info'),
      $container->get('entity_field.manager'),
      $container->get('logger.factory')->get('nexx_integration'),
      $container->get('token')
    );
  }

  /**
   * Endpoint for video creation / update.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   HTTP request.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   JSON response.
   *
   * @throws \Exception
   */
  public function video(Request $request) {
    $api_version = $this->config('nexx_integration.settings')
      ->get('nexx_api_version');

    if ($api_version != 2 || $api_version != 3) {
      $api_version = 3;
    }

    $controller_name = 'OmniaV' . $api_version . 'Controller';

    $controller = new $controller_name($this->database,
      $this->entityTypeBundleInfo,
      $this->entityFieldManager,
      $this->logger,
      $this->token
    );
    return $controller->video($request);
  }

  /**
   * Search and edit videos.
   */
  public function videoList() {
    $api_version = $this->config('nexx_integration.settings')
      ->get('nexx_api_version');

    if ($api_version != 2 || $api_version != 3) {
      $api_version = 3;
    }

    $controller_name = 'OmniaV' . $api_version . 'Controller';

    $controller = new $controller_name($this->database,
      $this->entityTypeBundleInfo,
      $this->entityFieldManager,
      $this->logger,
      $this->token
    );
    return $controller->videoList();
  }
}